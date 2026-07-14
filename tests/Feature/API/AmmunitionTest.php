<?php

namespace Tests\Feature\API;

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Reference\Purpose;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AmmunitionTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Caliber $caliber;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->caliber = Caliber::factory()->recycle($this->user)->create();
    }

    // index

    public function test_index_requires_authentication(): void
    {
        $this->getJson('/ammunition')->assertUnauthorized();
    }

    public function test_index_only_returns_current_users_ammunition(): void
    {
        $mine = Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create();
        Ammunition::factory()->create();

        $this->actingAs($this->user, 'api')
            ->getJson('/ammunition')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $mine->id);
    }

    public function test_index_filters_by_a_single_caliber(): void
    {
        $mine = Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create();

        $otherCaliber = Caliber::factory()->recycle($this->user)->create();
        Ammunition::factory()->recycle($this->user)->recycle($otherCaliber)->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/ammunition?filter[caliber_id]={$this->caliber->id}")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $mine->id);
    }

    public function test_index_filters_by_multiple_calibers(): void
    {
        $mine = Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create();

        $secondCaliber = Caliber::factory()->recycle($this->user)->create();
        $second = Ammunition::factory()->recycle($this->user)->recycle($secondCaliber)->create();

        $thirdCaliber = Caliber::factory()->recycle($this->user)->create();
        Ammunition::factory()->recycle($this->user)->recycle($thirdCaliber)->create();

        $ids = "{$this->caliber->id},{$secondCaliber->id}";

        $response = $this->actingAs($this->user, 'api')
            ->getJson("/ammunition?filter[caliber_id]={$ids}")
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $returnedIds = collect($response->json('data'))->pluck('id');
        $this->assertEqualsCanonicalizing([$mine->id, $second->id], $returnedIds->all());
    }

    public function test_index_excludes_zero_stock_when_in_stock_filter_is_applied(): void
    {
        $stocked = Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create(['inventory' => 250]);
        Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create(['inventory' => 0]);

        $this->actingAs($this->user, 'api')
            ->getJson('/ammunition?filter[in_stock]=1')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $stocked->id);
    }

    public function test_index_includes_zero_stock_without_the_in_stock_filter(): void
    {
        Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create(['inventory' => 250]);
        Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create(['inventory' => 0]);

        $this->actingAs($this->user, 'api')
            ->getJson('/ammunition')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    // store

    public function test_store_requires_authentication(): void
    {
        $this->postJson('/ammunition', [])->assertUnauthorized();
    }

    public function test_store_creates_ammunition(): void
    {
        $purpose = Purpose::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->postJson('/ammunition', [
                'caliber_id' => $this->caliber->id,
                'manufacturer' => 'Federal',
                'label' => '124gr FMJ',
                'purpose_id' => $purpose->id,
            ])
            ->assertOk()
            ->assertJsonPath('data.manufacturer', 'Federal');

        $this->assertDatabaseHas('cms.ammunition', [
            'manufacturer' => 'Federal',
            'caliber_id' => $this->caliber->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson('/ammunition', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['caliber_id', 'manufacturer', 'label']);
    }

    // show

    public function test_show_requires_authentication(): void
    {
        $ammo = Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create();
        $this->getJson("/ammunition/{$ammo->id}")->assertUnauthorized();
    }

    public function test_show_returns_ammunition(): void
    {
        $ammo = Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/ammunition/{$ammo->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $ammo->id);
    }

    public function test_show_returns_404_for_another_users_ammunition(): void
    {
        $other = Ammunition::factory()->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/ammunition/{$other->id}")
            ->assertNotFound();
    }

    // update

    public function test_update_requires_authentication(): void
    {
        $ammo = Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create();
        $this->putJson("/ammunition/{$ammo->id}", [])->assertUnauthorized();
    }

    public function test_update_modifies_ammunition(): void
    {
        $ammo = Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create();

        $this->actingAs($this->user, 'api')
            ->putJson("/ammunition/{$ammo->id}", [
                'manufacturer' => 'Speer',
                'label' => '147gr Gold Dot',
            ])
            ->assertOk()
            ->assertJsonPath('data.manufacturer', 'Speer');
    }

    public function test_update_returns_404_for_another_users_ammunition(): void
    {
        $other = Ammunition::factory()->create();

        $this->actingAs($this->user, 'api')
            ->putJson("/ammunition/{$other->id}", ['manufacturer' => 'X'])
            ->assertNotFound();
    }

    // destroy

    public function test_destroy_requires_authentication(): void
    {
        $ammo = Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create();
        $this->deleteJson("/ammunition/{$ammo->id}")->assertUnauthorized();
    }

    public function test_destroy_deletes_ammunition(): void
    {
        $ammo = Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create();

        $this->actingAs($this->user, 'api')
            ->deleteJson("/ammunition/{$ammo->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('cms.ammunition', ['id' => $ammo->id]);
    }

    // total

    public function test_total_returns_sum_of_inventory_rounds(): void
    {
        $ammo = Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/ammunition/{$ammo->id}/total")
            ->assertOk()
            ->assertJsonStructure(['data' => ['total']]);
    }

    // notes

    public function test_note_index_returns_empty_array(): void
    {
        $ammo = Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/ammunition/{$ammo->id}/notes")
            ->assertOk()
            ->assertJsonPath('data', []);
    }

    public function test_note_store_creates_note(): void
    {
        $ammo = Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create();

        $this->actingAs($this->user, 'api')
            ->postJson("/ammunition/{$ammo->id}/notes", ['note' => 'Great round'])
            ->assertCreated()
            ->assertJsonPath('data.note', 'Great round');

        $this->assertDatabaseHas('cms.notes', [
            'note' => 'Great round',
            'notable_id' => $ammo->id,
            'notable_type' => Ammunition::class,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_note_index_requires_authentication(): void
    {
        $ammo = Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create();
        $this->getJson("/ammunition/{$ammo->id}/notes")->assertUnauthorized();
    }

    public function test_note_store_requires_authentication(): void
    {
        $ammo = Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create();
        $this->postJson("/ammunition/{$ammo->id}/notes", ['note' => 'test'])->assertUnauthorized();
    }
}
