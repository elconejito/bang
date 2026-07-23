<?php

namespace Tests\Feature\API;

use App\Models\Firearm;
use App\Models\Light;
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
        $color = Color::factory()->recycle($this->user)->create(['label' => 'Flat Dark Earth']);
        Color::factory()->create();
        Firearm::factory()->recycle($this->user)->create(['color_id' => $color->id]);

        $this->actingAs($this->user, 'api')->getJson('/colors')
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.items_count', 1);
        $this->actingAs($this->user, 'api')->deleteJson("/colors/{$color->id}")->assertStatus(409);
    }

    public function test_firearm_and_all_accessory_types_accept_an_optional_color(): void
    {
        $color = Color::factory()->recycle($this->user)->create();
        $firearm = Firearm::factory()->recycle($this->user)->create(['color_id' => $color->id]);

        $this->actingAs($this->user, 'api')->getJson("/firearms/{$firearm->id}")
            ->assertOk()->assertJsonPath('data.color_id', $color->id);

        foreach ([Suppressor::class => '/suppressors', Optic::class => '/optics', Light::class => '/lights', MiscAccessory::class => '/misc-accessories'] as $model => $endpoint) {
            $asset = $model::factory()->recycle($this->user)->create(['color_id' => $color->id]);
            $this->actingAs($this->user, 'api')->getJson("{$endpoint}/{$asset->id}")
                ->assertOk()->assertJsonPath('data.color_id', $color->id)->assertJsonPath('data.color.label', $color->label);
        }
    }
}
