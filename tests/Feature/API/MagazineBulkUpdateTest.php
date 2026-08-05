<?php

namespace Tests\Feature\API;

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Firearm;
use App\Models\Location;
use App\Models\Magazine;
use App\Models\Reference\Color;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class MagazineBulkUpdateTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_bulk_update_requires_authentication(): void
    {
        $this->patchJson('/magazine-groups/1/magazines/bulk', [])->assertUnauthorized();
    }

    public function test_bulk_update_changes_selected_magazines_and_syncs_relationships(): void
    {
        [$first, $second] = $this->groupMagazines();
        $caliber = Caliber::factory()->recycle($this->user)->create();
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $color = Color::factory()->recycle($this->user)->create(['label' => 'Flat Dark Earth']);

        $this->bulk($first, [$first->id, $second->id], [
            'manufacturer' => 'Magpul',
            'model_name' => 'PMAG M3',
            'label' => 'Training',
            'color_id' => $color->id,
            'capacity' => 20,
            'calibers' => [$caliber->id],
            'firearms' => [$firearm->id],
        ])
            ->assertOk()
            ->assertJsonPath('data.updated_count', 2)
            ->assertJsonPath('meta.remaining_group_key', null)
            ->assertJsonPath('meta.updated_group_key', min($first->id, $second->id));

        foreach ([$first, $second] as $magazine) {
            $magazine->refresh();
            $this->assertSame('Magpul', $magazine->manufacturer);
            $this->assertSame('PMAG M3', $magazine->model_name);
            $this->assertSame('Training', $magazine->label);
            $this->assertSame($color->id, $magazine->color_id);
            $this->assertSame(20, $magazine->capacity);
            $this->assertSame([$caliber->id], $magazine->calibers()->pluck('id')->all());
            $this->assertSame([$firearm->id], $magazine->compatibleFirearms()->pluck('id')->all());
        }
    }

    public function test_bulk_update_can_explicitly_clear_nullable_fields(): void
    {
        [$first, $second] = $this->groupMagazines(['label' => 'Marked', 'model_name' => 'Gen 5']);
        $color = Color::factory()->recycle($this->user)->create();
        $first->update(['color_id' => $color->id]);
        $second->update(['color_id' => $color->id]);

        $this->bulk($first, [$first->id, $second->id], [
            'label' => null,
            'model_name' => null,
            'color_id' => null,
        ])->assertOk();

        foreach ([$first, $second] as $magazine) {
            $magazine->refresh();
            $this->assertNull($magazine->label);
            $this->assertNull($magazine->model_name);
            $this->assertNull($magazine->color_id);
        }
    }

    public function test_bulk_location_change_ejects_magazines_from_firearms(): void
    {
        [$first, $second] = $this->groupMagazines();
        $firstFirearm = Firearm::factory()->recycle($this->user)->create();
        $secondFirearm = Firearm::factory()->recycle($this->user)->create();
        $location = Location::factory()->recycle($this->user)->create();

        foreach ([[$first, $firstFirearm], [$second, $secondFirearm]] as [$magazine, $firearm]) {
            $magazine->compatibleFirearms()->attach($firearm);
            $magazine->update(['current_firearm_id' => $firearm->id]);
        }

        $this->bulk($first, [$first->id, $second->id], ['location_id' => $location->id])->assertOk();

        foreach ([$first, $second] as $magazine) {
            $magazine->refresh();
            $this->assertSame($location->id, $magazine->location_id);
            $this->assertNull($magazine->current_firearm_id);
        }
    }

    public function test_bulk_content_change_preserves_existing_location(): void
    {
        $caliber = Caliber::factory()->recycle($this->user)->create();
        $ammunition = Ammunition::factory()->recycle($this->user)->create(['caliber_id' => $caliber->id]);
        $location = Location::factory()->recycle($this->user)->create();
        [$first, $second] = $this->groupMagazines(['location_id' => $location->id]);
        $first->calibers()->attach($caliber);
        $second->calibers()->attach($caliber);

        $this->bulk($first, [$first->id, $second->id], [
            'loaded_ammunition_id' => $ammunition->id,
            'loaded_rounds' => 10,
        ])->assertOk();

        foreach ([$first, $second] as $magazine) {
            $magazine->refresh();
            $this->assertSame($location->id, $magazine->location_id);
            $this->assertNull($magazine->current_firearm_id);
            $this->assertSame($ammunition->id, $magazine->loaded_ammunition_id);
            $this->assertSame(10, $magazine->loaded_rounds);
        }

        $this->bulk($first, [$first->id, $second->id], [
            'loaded_ammunition_id' => null,
            'loaded_rounds' => 0,
        ])->assertOk();

        foreach ([$first, $second] as $magazine) {
            $magazine->refresh();
            $this->assertSame($location->id, $magazine->location_id);
            $this->assertNull($magazine->loaded_ammunition_id);
            $this->assertSame(0, $magazine->loaded_rounds);
        }
    }

    public function test_bulk_content_change_preserves_existing_firearm_placement(): void
    {
        $caliber = Caliber::factory()->recycle($this->user)->create();
        $ammunition = Ammunition::factory()->recycle($this->user)->create(['caliber_id' => $caliber->id]);
        $firearm = Firearm::factory()->recycle($this->user)->create();
        [$magazine] = $this->groupMagazines();
        $magazine->calibers()->attach($caliber);
        $magazine->compatibleFirearms()->attach($firearm);
        $magazine->update(['current_firearm_id' => $firearm->id]);

        $this->bulk($magazine, [$magazine->id], [
            'loaded_ammunition_id' => $ammunition->id,
            'loaded_rounds' => 10,
        ])->assertOk();

        $magazine->refresh();
        $this->assertSame($firearm->id, $magazine->current_firearm_id);
        $this->assertNull($magazine->location_id);
        $this->assertSame($ammunition->id, $magazine->loaded_ammunition_id);
        $this->assertSame(10, $magazine->loaded_rounds);
    }

    public function test_bulk_update_rejects_a_capacity_below_loaded_rounds_without_partial_updates(): void
    {
        $caliber = Caliber::factory()->recycle($this->user)->create();
        $ammunition = Ammunition::factory()->recycle($this->user)->create(['caliber_id' => $caliber->id]);
        [$first, $second] = $this->groupMagazines(['capacity' => 17, 'loaded_ammunition_id' => $ammunition->id, 'loaded_rounds' => 10]);
        $first->calibers()->attach($caliber);
        $second->calibers()->attach($caliber);

        $this->bulk($first, [$first->id, $second->id], [
            'manufacturer' => 'Changed',
            'capacity' => 9,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('changes.capacity');

        $this->assertSame('Glock', $first->fresh()->manufacturer);
        $this->assertSame('Glock', $second->fresh()->manufacturer);
        $this->assertSame(17, $first->fresh()->capacity);
        $this->assertSame(17, $second->fresh()->capacity);
    }

    public function test_bulk_update_rejects_removing_a_loaded_ammunitions_caliber(): void
    {
        $caliber = Caliber::factory()->recycle($this->user)->create();
        $ammunition = Ammunition::factory()->recycle($this->user)->create(['caliber_id' => $caliber->id]);
        [$first, $second] = $this->groupMagazines(['loaded_ammunition_id' => $ammunition->id, 'loaded_rounds' => 10]);
        $first->calibers()->attach($caliber);
        $second->calibers()->attach($caliber);

        $this->bulk($first, [$first->id, $second->id], ['calibers' => []])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('changes.calibers');

        $this->assertSame([$caliber->id], $first->fresh()->calibers()->pluck('id')->all());
        $this->assertSame([$caliber->id], $second->fresh()->calibers()->pluck('id')->all());
    }

    public function test_bulk_update_rejects_removing_the_compatibility_of_an_inserted_magazine(): void
    {
        [$first, $second] = $this->groupMagazines();
        $firstFirearm = Firearm::factory()->recycle($this->user)->create();
        $secondFirearm = Firearm::factory()->recycle($this->user)->create();

        foreach ([[$first, $firstFirearm], [$second, $secondFirearm]] as [$magazine, $firearm]) {
            $magazine->compatibleFirearms()->attach($firearm);
            $magazine->update(['current_firearm_id' => $firearm->id]);
        }

        $this->bulk($first, [$first->id, $second->id], ['firearms' => []])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('changes.firearms');

        $this->assertSame([$firstFirearm->id], $first->fresh()->compatibleFirearms()->pluck('id')->all());
        $this->assertSame([$secondFirearm->id], $second->fresh()->compatibleFirearms()->pluck('id')->all());
    }

    public function test_bulk_update_rejects_partial_content_changes_and_current_firearm_assignment(): void
    {
        [$first] = $this->groupMagazines();
        $firearm = Firearm::factory()->recycle($this->user)->create();

        $this->bulk($first, [$first->id], ['loaded_rounds' => 5])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['changes.loaded_ammunition_id', 'changes.loaded_rounds']);
        $this->bulk($first, [$first->id], [
            'loaded_ammunition_id' => null,
            'loaded_rounds' => 5,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('changes.loaded_ammunition_id');
        $this->bulk($first, [$first->id], [
            'current_firearm_id' => $firearm->id,
            'loaded_ammunition_id' => null,
            'loaded_rounds' => 0,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('changes.current_firearm_id');
    }

    public function test_bulk_update_rejects_foreign_and_other_group_magazines(): void
    {
        [$first] = $this->groupMagazines();
        $foreign = Magazine::factory()->create(['manufacturer' => 'Glock', 'capacity' => 17]);
        $otherGroup = Magazine::factory()->recycle($this->user)->create(['manufacturer' => 'Magpul', 'capacity' => 30]);

        $this->bulk($first, [$first->id, $foreign->id], ['label' => 'Nope'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('magazine_ids');
        $this->bulk($first, [$first->id, $otherGroup->id], ['label' => 'Nope'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('magazine_ids');
        $this->actingAs($this->user, 'api')
            ->patchJson("/magazine-groups/{$foreign->id}/magazines/bulk", [
                'magazine_ids' => [$first->id],
                'changes' => ['label' => 'Nope'],
            ])
            ->assertNotFound();
    }

    public function test_bulk_update_rejects_archived_magazines(): void
    {
        [$first, $second] = $this->groupMagazines();
        $second->update(['archived_at' => now(), 'archive_reason' => 'retired']);

        $this->bulk($first, [$first->id, $second->id], ['label' => 'Nope'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('magazine_ids');

        $this->assertSame('Glock', $first->fresh()->manufacturer);
    }

    public function test_bulk_update_returns_remaining_and_updated_representative_group_keys(): void
    {
        [$first, $second] = $this->groupMagazines();
        $remaining = Magazine::factory()->recycle($this->user)->create(['manufacturer' => 'Glock', 'model_name' => 'OEM', 'capacity' => 17]);

        $this->bulk($first, [$first->id, $second->id], ['manufacturer' => 'Magpul'])
            ->assertOk()
            ->assertJsonPath('meta.remaining_group_key', $remaining->id)
            ->assertJsonPath('meta.updated_group_key', min($first->id, $second->id));
    }

    /** @return array{0: Magazine, 1: Magazine} */
    private function groupMagazines(array $attributes = []): array
    {
        $defaults = ['manufacturer' => 'Glock', 'model_name' => 'OEM', 'capacity' => 17];
        $first = Magazine::factory()->recycle($this->user)->create([...$defaults, ...$attributes]);
        $second = Magazine::factory()->recycle($this->user)->create([...$defaults, ...$attributes]);

        return [$first, $second];
    }

    /** @param list<int> $magazineIds */
    private function bulk(Magazine $group, array $magazineIds, array $changes): TestResponse
    {
        return $this->actingAs($this->user, 'api')->patchJson("/magazine-groups/{$group->id}/magazines/bulk", [
            'magazine_ids' => $magazineIds,
            'changes' => $changes,
        ]);
    }
}
