<?php

namespace Tests\Feature\API;

use App\Models\ActivityEvent;
use App\Models\Firearm;
use App\Models\Light;
use App\Models\MiscAccessory;
use App\Models\Optic;
use App\Models\Suppressor;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class FirearmMountingTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Firearm $firearm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->firearm = Firearm::factory()->recycle($this->user)->create();
    }

    public function test_initially_mounted_accessories_record_added_and_mount_events_for_all_accessory_types(): void
    {
        $accessories = [
            Suppressor::factory()->recycle($this->user)->create(['firearm_id' => $this->firearm->id]),
            Optic::factory()->recycle($this->user)->create(['firearm_id' => $this->firearm->id]),
            Light::factory()->recycle($this->user)->create(['firearm_id' => $this->firearm->id]),
            MiscAccessory::factory()->recycle($this->user)->create(['firearm_id' => $this->firearm->id, 'sub_type' => 'sling']),
        ];

        foreach ($accessories as $accessory) {
            $events = ActivityEvent::withoutGlobalScopes()
                ->where('subject_type', $accessory->getMorphClass())
                ->where('subject_id', $accessory->id)
                ->orderBy('id')
                ->pluck('type')
                ->all();

            $this->assertSame(['ADDED', 'MOUNT'], $events);
        }

        $this->actingAs($this->user, 'api')
            ->getJson("/firearms/{$this->firearm->id}/activity?filter[type]=MOUNT")
            ->assertOk()
            ->assertJsonCount(4, 'data');
    }

    public function test_mountable_and_batch_endpoints_require_authentication(): void
    {
        $this->getJson("/firearms/{$this->firearm->id}/mountable-accessories")->assertUnauthorized();
        $this->postJson("/firearms/{$this->firearm->id}/mount-accessories", ['accessories' => []])->assertUnauthorized();
    }

    public function test_mountable_accessories_excludes_mounted_archived_other_user_and_fits_misc_items(): void
    {
        $eligible = Optic::factory()->recycle($this->user)->create();
        $mounted = Light::factory()->recycle($this->user)->create(['firearm_id' => $this->firearm->id]);
        $archived = Suppressor::factory()->recycle($this->user)->create();
        $archived->forceFill(['archived_at' => now()])->save();
        $fits = MiscAccessory::factory()->recycle($this->user)->create(['sub_type' => 'holster']);
        $unclassified = MiscAccessory::factory()->recycle($this->user)->create(['sub_type' => null]);
        $other = Optic::factory()->create();

        $response = $this->actingAs($this->user, 'api')
            ->getJson("/firearms/{$this->firearm->id}/mountable-accessories")
            ->assertOk()
            ->assertJsonFragment(['type' => 'Optic', 'id' => $eligible->id])
            ->assertJsonFragment(['type' => 'Misc', 'id' => $unclassified->id]);

        $items = collect($response->json('data'));
        $this->assertFalse($items->contains(fn (array $item): bool => $item['type'] === 'Light' && $item['id'] === $mounted->id));
        $this->assertFalse($items->contains(fn (array $item): bool => $item['type'] === 'Suppressor' && $item['id'] === $archived->id));
        $this->assertFalse($items->contains(fn (array $item): bool => $item['type'] === 'Misc' && $item['id'] === $fits->id));
        $this->assertFalse($items->contains(fn (array $item): bool => $item['type'] === 'Optic' && $item['id'] === $other->id));
    }

    public function test_batch_mounts_accessories_and_rolls_back_when_any_selected_item_is_invalid(): void
    {
        $optic = Optic::factory()->recycle($this->user)->create();
        $light = Light::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->postJson("/firearms/{$this->firearm->id}/mount-accessories", [
                'accessories' => [
                    ['type' => 'Optic', 'id' => $optic->id],
                    ['type' => 'Light', 'id' => $light->id],
                ],
            ])
            ->assertNoContent();

        $this->assertSame($this->firearm->id, $optic->refresh()->firearm_id);
        $this->assertSame($this->firearm->id, $light->refresh()->firearm_id);
        $this->assertSame(1, ActivityEvent::query()->where('subject_type', Optic::class)->where('subject_id', $optic->id)->where('type', 'MOUNT')->count());
        $this->assertSame(1, ActivityEvent::query()->where('subject_type', Light::class)->where('subject_id', $light->id)->where('type', 'MOUNT')->count());

        $unmounted = Suppressor::factory()->recycle($this->user)->create();
        $otherUsersAccessory = Optic::factory()->create();

        $this->actingAs($this->user, 'api')
            ->postJson("/firearms/{$this->firearm->id}/mount-accessories", [
                'accessories' => [
                    ['type' => 'Suppressor', 'id' => $unmounted->id],
                    ['type' => 'Optic', 'id' => $otherUsersAccessory->id],
                ],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('accessories');

        $this->assertNull($unmounted->refresh()->firearm_id);
    }

    public function test_archived_firearms_and_duplicate_tokens_cannot_be_used_for_batch_mounting(): void
    {
        $optic = Optic::factory()->recycle($this->user)->create();
        $this->firearm->forceFill(['archived_at' => now()])->save();

        $this->actingAs($this->user, 'api')
            ->postJson("/firearms/{$this->firearm->id}/mount-accessories", [
                'accessories' => [['type' => 'Optic', 'id' => $optic->id]],
            ])
            ->assertConflict()
            ->assertJsonPath('code', 'archived_item_activity_blocked');

        $this->firearm->forceFill(['archived_at' => null])->save();

        $this->actingAs($this->user, 'api')
            ->postJson("/firearms/{$this->firearm->id}/mount-accessories", [
                'accessories' => [
                    ['type' => 'Optic', 'id' => $optic->id],
                    ['type' => 'Optic', 'id' => $optic->id],
                ],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('accessories');

        $this->assertNull($optic->refresh()->firearm_id);
    }
}
