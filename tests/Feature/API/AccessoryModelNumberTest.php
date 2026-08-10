<?php

namespace Tests\Feature\API;

use App\Models\Light;
use App\Models\Magazine;
use App\Models\MiscAccessory;
use App\Models\Mount;
use App\Models\Optic;
use App\Models\Suppressor;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AccessoryModelNumberTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @param  class-string<Model>  $modelClass
     */
    #[DataProvider('accessoryTypes')]
    public function test_model_number_can_be_updated_and_is_returned_by_the_api(
        string $modelClass,
        string $endpoint,
    ): void {
        $user = User::factory()->create();
        $accessory = $modelClass::factory()->recycle($user)->create(['model_number' => null]);

        $this->actingAs($user, 'api')
            ->putJson("/{$endpoint}/{$accessory->getKey()}", ['model_number' => 'SKU-1234'])
            ->assertOk()
            ->assertJsonPath('data.model_number', 'SKU-1234');

        $this->assertSame('SKU-1234', $accessory->refresh()->model_number);
    }

    /**
     * @return array<string, array{class-string<Model>, string}>
     */
    public static function accessoryTypes(): array
    {
        return [
            'suppressor' => [Suppressor::class, 'suppressors'],
            'optic' => [Optic::class, 'optics'],
            'light' => [Light::class, 'lights'],
            'miscellaneous accessory' => [MiscAccessory::class, 'misc-accessories'],
            'mount' => [Mount::class, 'mounts'],
            'magazine' => [Magazine::class, 'magazines'],
        ];
    }
}
