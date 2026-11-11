<?php

namespace Tests\Feature\API;

use App\Models\Ammunition;
use App\Models\Reference\BulletType;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class BulletTypeTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_bullet_types_require_authentication(): void
    {
        $this->postJson('/bullet-type', [])->assertUnauthorized();
    }

    public function test_bullet_types_can_be_created_updated_and_listed(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/bullet-type', ['label' => 'Total Metal Jacket', 'abbreviation' => 'TMJ'])
            ->assertOk()
            ->assertJsonPath('data.label', 'Total Metal Jacket')
            ->assertJsonPath('data.abbreviation', 'TMJ');

        $bulletTypeId = $response->json('data.id');

        $this->actingAs($this->user, 'api')
            ->putJson("/bullet-type/{$bulletTypeId}", [
                'label' => 'Total Metal Jacket',
                'abbreviation' => 'TMJ+',
            ])
            ->assertOk()
            ->assertJsonPath('data.abbreviation', 'TMJ+');

        $this->actingAs($this->user, 'api')
            ->putJson("/bullet-type/{$bulletTypeId}", [
                'label' => 'Jacketed Hollow Point',
                'abbreviation' => 'JHP',
            ])
            ->assertOk()
            ->assertJsonPath('data.label', 'Jacketed Hollow Point')
            ->assertJsonPath('data.abbreviation', 'JHP');

        $this->actingAs($this->user, 'api')
            ->getJson('/bullet-type')
            ->assertOk()
            ->assertJsonFragment(['id' => $bulletTypeId, 'loads_count' => 0]);
    }

    public function test_bullet_type_validation_requires_unique_values_within_length_limits(): void
    {
        BulletType::create(['label' => 'Bonded Hollow Point', 'abbreviation' => 'BHP']);

        $this->actingAs($this->user, 'api')
            ->postJson('/bullet-type', ['label' => 'Bonded Hollow Point', 'abbreviation' => 'BHP'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['label']);

        $this->actingAs($this->user, 'api')
            ->postJson('/bullet-type', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['label', 'abbreviation']);

        $this->actingAs($this->user, 'api')
            ->postJson('/bullet-type', ['label' => str_repeat('a', 256), 'abbreviation' => str_repeat('b', 256)])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['label', 'abbreviation']);
    }

    public function test_bullet_type_cannot_be_renamed_to_an_existing_label(): void
    {
        $bulletType = BulletType::create(['label' => 'Total Metal Jacket', 'abbreviation' => 'TMJ']);
        BulletType::create(['label' => 'Jacketed Hollow Point', 'abbreviation' => 'JHP']);

        $this->actingAs($this->user, 'api')
            ->putJson("/bullet-type/{$bulletType->id}", ['label' => 'Jacketed Hollow Point'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['label']);
    }

    public function test_unused_bullet_types_can_be_deleted(): void
    {
        $bulletType = BulletType::create(['label' => 'Wad Cutter', 'abbreviation' => 'WC']);

        $this->actingAs($this->user, 'api')
            ->deleteJson("/bullet-type/{$bulletType->id}")
            ->assertNoContent();

        $this->assertModelMissing($bulletType);
    }

    public function test_bullet_types_in_use_by_another_user_cannot_be_deleted_and_are_counted(): void
    {
        $bulletType = BulletType::create(['label' => 'Copper Plated Round Nose', 'abbreviation' => 'CPRN']);
        Ammunition::factory()->recycle(User::factory()->create())->create(['bullet_type_id' => $bulletType->id]);

        $this->actingAs($this->user, 'api')
            ->getJson('/bullet-type')
            ->assertOk()
            ->assertJsonFragment(['id' => $bulletType->id, 'loads_count' => 1]);

        $this->actingAs($this->user, 'api')
            ->deleteJson("/bullet-type/{$bulletType->id}")
            ->assertStatus(409);

        $this->assertModelExists($bulletType);
    }
}
