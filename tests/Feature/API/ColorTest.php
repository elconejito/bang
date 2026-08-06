<?php

namespace Tests\Feature\API;

use App\Models\Firearm;
use App\Models\Light;
use App\Models\Magazine;
use App\Models\MiscAccessory;
use App\Models\Optic;
use App\Models\Reference\Color;
use App\Models\Suppressor;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ColorTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_colors_are_managed_per_user_and_cannot_be_deleted_while_in_use(): void
    {
        $color = Color::factory()->recycle($this->user)->create([
            'label' => 'Flat Dark Earth',
            'short_label' => 'FDE',
        ]);
        Color::factory()->create();
        Firearm::factory()->recycle($this->user)->create(['color_id' => $color->id]);

        $this->actingAs($this->user, 'api')->getJson('/colors')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.short_label', 'FDE')
            ->assertJsonPath('data.0.items_count', 1);
        $this->actingAs($this->user, 'api')->deleteJson("/colors/{$color->id}")->assertStatus(409);
    }

    public function test_colors_require_a_short_label_when_created_and_can_update_it(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson('/colors', ['label' => 'Flat Dark Earth'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('short_label');

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/colors', ['label' => 'Flat Dark Earth', 'short_label' => 'FDE'])
            ->assertOk()
            ->assertJsonPath('data.label', 'Flat Dark Earth')
            ->assertJsonPath('data.short_label', 'FDE');

        $this->actingAs($this->user, 'api')
            ->putJson("/colors/{$response->json('data.id')}", ['short_label' => 'FDE2'])
            ->assertOk()
            ->assertJsonPath('data.short_label', 'FDE2');
    }

    public function test_firearm_and_all_accessory_types_accept_an_optional_color(): void
    {
        $color = Color::factory()->recycle($this->user)->create();
        $firearm = Firearm::factory()->recycle($this->user)->create(['color_id' => $color->id]);

        $this->actingAs($this->user, 'api')->getJson("/firearms/{$firearm->id}")
            ->assertOk()->assertJsonPath('data.color_id', $color->id);

        foreach ([Suppressor::class => '/suppressors', Optic::class => '/optics', Light::class => '/lights', MiscAccessory::class => '/misc-accessories', Magazine::class => '/magazines'] as $model => $endpoint) {
            $asset = $model::factory()->recycle($this->user)->create(['color_id' => $color->id]);
            $this->actingAs($this->user, 'api')->getJson("{$endpoint}/{$asset->id}")
                ->assertOk()->assertJsonPath('data.color_id', $color->id)->assertJsonPath('data.color.label', $color->label);
        }
    }
}
