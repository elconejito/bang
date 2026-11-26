<?php

namespace Tests\Feature\API;

use App\Actions\Magazines\CreateMagazineBatch;
use App\Models\Caliber;
use App\Models\Firearm;
use App\Models\Location;
use App\Models\Magazine;
use App\Models\Order;
use App\Models\Reference\Color;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MagazineBatchTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_batch_creation_requires_authentication(): void
    {
        $this->postJson('/magazine-batches', [])->assertUnauthorized();
    }

    public function test_batch_creation_generates_padded_markings_and_assigns_relationships(): void
    {
        $user = User::factory()->create();
        $caliber = Caliber::factory()->recycle($user)->create();
        $firearm = Firearm::factory()->recycle($user)->create();
        $location = Location::factory()->recycle($user)->create();
        $color = Color::factory()->recycle($user)->create();
        $order = Order::create([
            'user_id' => $user->id,
            'order_date' => '2026-09-12',
            'rounds' => 0,
            'total_cost' => 0,
        ]);

        $this->actingAs($user, 'api')->postJson('/magazine-batches', [
            'manufacturer' => 'Magpul',
            'model_name' => 'PMAG',
            'model_number' => 'MAG-123',
            'capacity' => 30,
            'quantity' => 3,
            'marking_prefix' => 'AR-',
            'marking_start' => 8,
            'marking_width' => 3,
            'calibers' => [$caliber->id],
            'firearms' => [$firearm->id],
            'location_id' => $location->id,
            'color_id' => $color->id,
            'order_id' => $order->id,
            'cost' => 13.50,
            'items' => [
                ['serial_number' => 'SN-001', 'id_marking' => 'CUSTOM-001'],
                ['serial_number' => 'SN-002', 'id_marking' => ''],
                ['serial_number' => '', 'id_marking' => 'CUSTOM-003'],
            ],
        ])->assertCreated()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.id_marking', 'CUSTOM-001')
            ->assertJsonPath('data.0.serial_number', 'SN-001')
            ->assertJsonPath('data.1.id_marking', 'AR-009')
            ->assertJsonPath('data.2.id_marking', 'CUSTOM-003')
            ->assertJsonPath('data.0.model_number', 'MAG-123')
            ->assertJsonPath('data.0.color_id', $color->id)
            ->assertJsonPath('meta.created', 3)
            ->assertJsonPath('meta.first_marking', 'CUSTOM-001')
            ->assertJsonPath('meta.last_marking', 'CUSTOM-003');

        $magazines = Magazine::query()->where('user_id', $user->id)->orderBy('id')->get();
        $this->assertSame(['CUSTOM-001', 'AR-009', 'CUSTOM-003'], $magazines->pluck('id_marking')->all());
        $this->assertSame(['SN-001', 'SN-002', null], $magazines->pluck('serial_number')->all());
        $this->assertTrue($magazines->every(fn (Magazine $magazine): bool => $magazine->model_number === 'MAG-123'));
        $this->assertTrue($magazines->every(fn (Magazine $magazine): bool => $magazine->location_id === $location->id));
        $this->assertTrue($magazines->every(fn (Magazine $magazine): bool => $magazine->calibers()->whereKey($caliber->id)->exists()));
        $this->assertTrue($magazines->every(fn (Magazine $magazine): bool => $magazine->compatibleFirearms()->whereKey($firearm->id)->exists()));
        $this->assertSame(3, $order->orderAssets()->count());
        $this->assertTrue($order->orderAssets->every(fn ($asset): bool => $asset->cost === '13.50'));
    }

    public function test_batch_creation_without_a_prefix_leaves_markings_null(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'api')->postJson('/magazine-batches', [
            'manufacturer' => 'Glock',
            'capacity' => 17,
            'quantity' => 2,
        ])->assertCreated()
            ->assertJsonPath('meta.first_marking', null)
            ->assertJsonPath('meta.last_marking', null);

        $this->assertSame([null, null], Magazine::query()->pluck('id_marking')->all());
    }

    public function test_batch_creation_accepts_explicitly_null_automarking_fields(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'api')->postJson('/magazine-batches', [
            'manufacturer' => 'Glock',
            'capacity' => 17,
            'quantity' => 2,
            'marking_prefix' => null,
            'marking_start' => null,
            'marking_width' => null,
        ])->assertCreated();

        $this->assertSame([null, null], Magazine::query()->pluck('id_marking')->all());
    }

    public function test_batch_creation_rejects_cross_user_relationships_without_writing(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $caliber = Caliber::factory()->recycle($otherUser)->create();
        $firearm = Firearm::factory()->recycle($otherUser)->create();
        $location = Location::factory()->recycle($otherUser)->create();

        $this->actingAs($user, 'api')->postJson('/magazine-batches', [
            'manufacturer' => 'Glock',
            'capacity' => 17,
            'quantity' => 2,
            'calibers' => [$caliber->id],
            'firearms' => [$firearm->id],
            'location_id' => $location->id,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['calibers.0', 'firearms.0', 'location_id']);

        $this->assertDatabaseCount('cms.magazines', 0);
    }

    public function test_batch_creation_requires_an_item_for_each_magazine(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'api')->postJson('/magazine-batches', [
            'manufacturer' => 'Glock',
            'capacity' => 17,
            'quantity' => 2,
            'items' => [['serial_number' => 'SN-001']],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('items');
    }

    public function test_action_rolls_back_the_entire_batch_when_a_later_insert_fails(): void
    {
        $user = User::factory()->create();
        DB::statement("ALTER TABLE cms.magazines ADD CONSTRAINT magazines_test_reject_marking CHECK (id_marking IS DISTINCT FROM 'FAIL-002')");

        try {
            $this->expectException(QueryException::class);

            app(CreateMagazineBatch::class)->handle($user, [
                'manufacturer' => 'Glock',
                'capacity' => 17,
                'quantity' => 2,
                'marking_prefix' => 'FAIL-',
                'marking_start' => 1,
                'marking_width' => 3,
            ]);
        } finally {
            $this->assertDatabaseCount('cms.magazines', 0);
            DB::statement('ALTER TABLE cms.magazines DROP CONSTRAINT IF EXISTS magazines_test_reject_marking');
        }
    }
}
