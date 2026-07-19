<?php

namespace Tests\Feature\API;

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Firearm;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Reference\Purpose;
use App\Models\User;
use Carbon\CarbonImmutable;
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

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
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

    public function test_show_returns_active_firearms_that_use_the_ammunition_caliber(): void
    {
        $ammo = Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create();
        $firearm = Firearm::factory()->recycle($this->user)->create(['manufacturer' => 'Glock', 'label' => 'G19']);
        $firearm->calibers()->attach($this->caliber);

        $this->actingAs($this->user, 'api')
            ->getJson("/ammunition/{$ammo->id}")
            ->assertOk()
            ->assertJsonPath('data.used_by_firearms.0.id', $firearm->id)
            ->assertJsonPath('data.used_by_firearms.0.label', 'G19');
    }

    public function test_show_does_not_return_other_users_firearms_for_used_by(): void
    {
        $ammo = Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create();
        $otherFirearm = Firearm::factory()->create();
        $otherFirearm->calibers()->attach($this->caliber->id);

        $this->actingAs($this->user, 'api')
            ->getJson("/ammunition/{$ammo->id}")
            ->assertOk()
            ->assertJsonPath('data.used_by_firearms', []);
    }

    public function test_show_does_not_return_archived_firearms_for_used_by(): void
    {
        $ammo = Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create();
        $firearm = Firearm::factory()->recycle($this->user)->create(['archived_at' => now()]);
        $firearm->calibers()->attach($this->caliber);

        $this->actingAs($this->user, 'api')
            ->getJson("/ammunition/{$ammo->id}")
            ->assertOk()
            ->assertJsonPath('data.used_by_firearms', []);
    }

    public function test_stats_requires_authentication_and_ownership(): void
    {
        $mine = Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create();
        $other = Ammunition::factory()->create();

        $this->getJson("/ammunition/{$mine->id}/stats")->assertUnauthorized();
        $this->actingAs($this->user, 'api')->getJson("/ammunition/{$other->id}/stats")->assertNotFound();
    }

    public function test_stats_returns_twelve_empty_months_without_purchase_values(): void
    {
        CarbonImmutable::setTestNow('2026-07-18 12:00:00');
        $ammo = Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create(['inventory' => 0]);

        $this->actingAs($this->user, 'api')
            ->getJson("/ammunition/{$ammo->id}/stats")
            ->assertOk()
            ->assertJsonCount(12, 'data.months')
            ->assertJsonPath('data.months.0.key', '2025-08')
            ->assertJsonPath('data.months.11.key', '2026-07')
            ->assertJsonPath('data.months.11.on_hand', 0)
            ->assertJsonPath('data.average_purchase_cost_per_round', null)
            ->assertJsonPath('data.estimated_current_value', null)
            ->assertJsonPath('data.purchase_cost_range', null);
    }

    public function test_stats_uses_full_history_and_month_end_balances(): void
    {
        CarbonImmutable::setTestNow('2026-07-18 12:00:00');
        $ammo = Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create(['inventory' => 320]);
        $order = Order::create([
            'order_date' => '2025-07-31',
            'rounds' => 300,
            'total_cost' => 85,
            'user_id' => $this->user->id,
        ]);

        Inventory::factory()->recycle($this->user)->recycle($ammo)->create([
            'inventory_date' => '2025-07-31', 'rounds' => 100, 'cost' => 25, 'order_id' => $order->id,
        ]);
        Inventory::factory()->recycle($this->user)->recycle($ammo)->create([
            'inventory_date' => '2025-08-01', 'rounds' => 200, 'cost' => 60, 'order_id' => $order->id,
        ]);
        Inventory::factory()->recycle($this->user)->recycle($ammo)->create([
            'inventory_date' => '2025-08-31', 'rounds' => -50, 'cost' => 999,
        ]);
        Inventory::factory()->recycle($this->user)->recycle($ammo)->create([
            'inventory_date' => '2026-07-01', 'rounds' => 70, 'cost' => 0,
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson("/ammunition/{$ammo->id}/stats")
            ->assertOk()
            ->assertJsonCount(12, 'data.months')
            ->assertJsonPath('data.months.0.key', '2025-08')
            ->assertJsonPath('data.months.0.on_hand', 250)
            ->assertJsonPath('data.months.0.purchase_cost_per_round', 0.3)
            ->assertJsonPath('data.months.11.on_hand', 320)
            ->assertJsonPath('data.average_purchase_cost_per_round', 85 / 300)
            ->assertJsonPath('data.estimated_current_value', (85 / 300) * 320);

        $this->assertSame(250, $response->json('data.months.1.on_hand'));
        CarbonImmutable::setTestNow();
    }

    public function test_stats_aggregates_more_than_two_hundred_entries(): void
    {
        CarbonImmutable::setTestNow('2026-07-18 12:00:00');
        $ammo = Ammunition::factory()->recycle($this->user)->recycle($this->caliber)->create(['inventory' => 201]);
        $order = Order::create([
            'order_date' => '2026-07-01',
            'rounds' => 201,
            'total_cost' => 100.5,
            'user_id' => $this->user->id,
        ]);
        Inventory::factory()->count(201)->recycle($this->user)->recycle($ammo)->create([
            'inventory_date' => '2026-07-01', 'rounds' => 1, 'cost' => 0.5, 'order_id' => $order->id,
        ]);

        $this->actingAs($this->user, 'api')
            ->getJson("/ammunition/{$ammo->id}/stats")
            ->assertOk()
            ->assertJsonPath('data.months.11.on_hand', 201)
            ->assertJsonPath('data.average_purchase_cost_per_round', 0.5)
            ->assertJsonPath('data.estimated_current_value', 100.5);

        CarbonImmutable::setTestNow();
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
