<?php

namespace Tests\Feature\API;

use App\Models\Ammunition;
use App\Models\Reference\ShellType;
use App\Models\Reference\ShotMaterial;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ShotgunReferenceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public static function referenceLists(): array
    {
        return [
            'shell types' => ['shell-type', ShellType::class, 'shell_type_id'],
            'shot materials' => ['shot-material', ShotMaterial::class, 'shot_material_id'],
        ];
    }

    #[DataProvider('referenceLists')]
    public function test_lists_require_authentication(string $endpoint, string $modelClass, string $foreignKey): void
    {
        $this->getJson("/{$endpoint}")->assertUnauthorized();
        $this->postJson("/{$endpoint}", [])->assertUnauthorized();
        $this->putJson("/{$endpoint}/1", [])->assertUnauthorized();
        $this->deleteJson("/{$endpoint}/1")->assertUnauthorized();
    }

    #[DataProvider('referenceLists')]
    public function test_entries_can_be_created_renamed_listed_and_deleted(string $endpoint, string $modelClass, string $foreignKey): void
    {
        $this->actingAs(User::factory()->create(), 'api');

        $id = $this->postJson("/{$endpoint}", ['label' => 'Custom option'])
            ->assertOk()->assertJsonPath('data.label', 'Custom option')
            ->json('data.id');

        $this->putJson("/{$endpoint}/{$id}", ['label' => 'Custom option'])
            ->assertOk()->assertJsonPath('data.label', 'Custom option');
        $this->putJson("/{$endpoint}/{$id}", ['label' => 'Renamed option'])
            ->assertOk()->assertJsonPath('data.label', 'Renamed option');
        $this->getJson("/{$endpoint}")->assertOk()
            ->assertJsonFragment(['id' => $id, 'label' => 'Renamed option', 'loads_count' => 0]);

        $entry = $modelClass::findOrFail($id);
        $this->deleteJson("/{$endpoint}/{$id}")->assertNoContent();
        $this->assertModelMissing($entry);
    }

    #[DataProvider('referenceLists')]
    public function test_labels_are_required_unique_and_limited_in_length(string $endpoint, string $modelClass, string $foreignKey): void
    {
        $this->actingAs(User::factory()->create(), 'api');
        $entry = $modelClass::create(['label' => 'Existing option']);
        $other = $modelClass::create(['label' => 'Other option']);

        foreach (['', str_repeat('a', 256), 'Existing option'] as $label) {
            $this->postJson("/{$endpoint}", ['label' => $label])
                ->assertUnprocessable()->assertJsonValidationErrors('label');
            $this->putJson("/{$endpoint}/{$other->id}", ['label' => $label])
                ->assertUnprocessable()->assertJsonValidationErrors('label');
        }

        $this->postJson("/{$endpoint}", [])->assertUnprocessable()->assertJsonValidationErrors('label');
        $this->assertModelExists($entry);
    }

    #[DataProvider('referenceLists')]
    public function test_usage_across_users_prevents_deletion_and_renaming_preserves_links(string $endpoint, string $modelClass, string $foreignKey): void
    {
        $entry = $modelClass::create(['label' => 'Shared option']);
        $ammunition = Ammunition::factory()->recycle(User::factory()->create())->create([$foreignKey => $entry->id]);
        $this->actingAs(User::factory()->create(), 'api');

        $this->getJson("/{$endpoint}")->assertOk()
            ->assertJsonFragment(['id' => $entry->id, 'loads_count' => 1]);
        $this->deleteJson("/{$endpoint}/{$entry->id}")->assertStatus(409);
        $this->putJson("/{$endpoint}/{$entry->id}", ['label' => 'Renamed shared option'])
            ->assertOk()->assertJsonPath('data.label', 'Renamed shared option');

        $this->assertSame($entry->id, $ammunition->fresh()->{$foreignKey});
        $this->assertModelExists($entry);
    }

    #[DataProvider('referenceLists')]
    public function test_missing_entries_return_not_found(string $endpoint, string $modelClass, string $foreignKey): void
    {
        $this->actingAs(User::factory()->create(), 'api');
        $this->putJson("/{$endpoint}/2147483647", ['label' => 'Missing'])->assertNotFound();
        $this->deleteJson("/{$endpoint}/2147483647")->assertNotFound();
    }
}
