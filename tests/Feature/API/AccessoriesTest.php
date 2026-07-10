<?php

namespace Tests\Feature\API;

use App\Models\Caliber;
use App\Models\Magazine;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AccessoriesTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_accessories_returns_compact_magazine_groups_without_individual_rows(): void
    {
        $user = User::factory()->create();
        $caliber = Caliber::factory()->create();
        $magazines = Magazine::factory()->recycle($user)->count(2)->create([
            'manufacturer' => 'Glock',
            'model_name' => 'OEM',
            'capacity' => 17,
        ]);
        $magazines->each(fn (Magazine $magazine) => $magazine->calibers()->attach($caliber));

        $this->actingAs($user, 'api')
            ->getJson('/accessories')
            ->assertOk()
            ->assertJsonCount(1, 'data.magazines')
            ->assertJsonPath('data.magazines.0.summary.total', 2)
            ->assertJsonMissingPath('data.magazines.0.magazines')
            ->assertJsonMissingPath('data.magazines.0.id');
    }
}
