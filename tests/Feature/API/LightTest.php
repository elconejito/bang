<?php

namespace Tests\Feature\API;

use App\Models\Reference\Color;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class LightTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_store_accepts_optional_laser_and_beam_pattern_values(): void
    {
        $this->actingAs($this->user, 'api')->postJson('/lights', [
            'manufacturer' => 'Streamlight',
            'label' => 'TLR-8',
            'laser' => 'green',
            'beam_pattern' => 'mixed',
        ])->assertOk()
            ->assertJsonPath('data.laser', 'green')
            ->assertJsonPath('data.beam_pattern', 'mixed');
    }

    public function test_store_accepts_a_reference_color(): void
    {
        $color = Color::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')->postJson('/lights', [
            'manufacturer' => 'Streamlight',
            'label' => 'TLR-8',
            'color_id' => $color->id,
        ])->assertOk()
            ->assertJsonPath('data.color.id', $color->id);
    }

    public function test_store_defaults_laser_fields_to_null_and_rejects_invalid_values(): void
    {
        $payload = ['manufacturer' => 'SureFire', 'label' => 'X300'];

        $this->actingAs($this->user, 'api')->postJson('/lights', $payload)
            ->assertOk()
            ->assertJsonPath('data.laser', null)
            ->assertJsonPath('data.beam_pattern', null);

        $this->actingAs($this->user, 'api')->postJson('/lights', [
            ...$payload,
            'laser' => 'blue',
            'beam_pattern' => 'wide',
        ])->assertUnprocessable()->assertJsonValidationErrors(['laser', 'beam_pattern']);
    }
}
