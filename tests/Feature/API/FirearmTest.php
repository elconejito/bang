<?php

namespace Tests\Feature\API;

use App\Models\Firearm;
use App\Models\Light;
use App\Models\Magazine;
use App\Models\Optic;
use App\Models\Reference\Color;
use App\Models\Suppressor;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class FirearmTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // index

    public function test_index_requires_authentication(): void
    {
        $this->getJson('/firearms')->assertUnauthorized();
    }

    public function test_index_returns_only_authenticated_users_firearms(): void
    {
        Firearm::factory()->recycle($this->user)->create();
        Firearm::factory()->create(); // another user

        $this->actingAs($this->user, 'api')
            ->getJson('/firearms')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_index_returns_mounted_accessories_for_firearm_cards(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        Optic::factory()->recycle($this->user)->create([
            'firearm_id' => $firearm->id,
            'label' => 'Holosun 507C',
        ]);
        Suppressor::factory()->recycle($this->user)->create([
            'firearm_id' => $firearm->id,
            'label' => 'Omega 9K',
        ]);

        $this->actingAs($this->user, 'api')
            ->getJson('/firearms')
            ->assertOk()
            ->assertJsonPath('data.0.mounted_accessories.0.type', 'Suppressor')
            ->assertJsonPath('data.0.mounted_accessories.1.type', 'Optic');
    }

    // store

    public function test_store_requires_authentication(): void
    {
        $this->postJson('/firearms', [])->assertUnauthorized();
    }

    public function test_store_creates_firearm(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson('/firearms', [
                'manufacturer' => 'Beretta',
                'model' => '1301 Tactical C Gray 7+1',
                'type' => 'shotgun',
                'customizer' => 'Langdon Tactical',
                'custom_package' => 'LTT Trigger Job',
                'label' => 'My 1301',
            ])
            ->assertOk()
            ->assertJsonPath('data.manufacturer', 'Beretta')
            ->assertJsonPath('data.type', 'shotgun')
            ->assertJsonPath('data.type_label', 'Shotgun')
            ->assertJsonPath('data.customizer', 'Langdon Tactical')
            ->assertJsonPath('data.custom_package', 'LTT Trigger Job');

        $this->assertDatabaseHas('cms.firearms', [
            'manufacturer' => 'Beretta',
            'type' => 'shotgun',
            'customizer' => 'Langdon Tactical',
            'custom_package' => 'LTT Trigger Job',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_store_creates_firearm_with_a_user_color(): void
    {
        $color = Color::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->postJson('/firearms', [
                'manufacturer' => 'Beretta',
                'model' => '1301 Tactical',
                'label' => 'My 1301',
                'color_id' => $color->id,
            ])
            ->assertOk()
            ->assertJsonPath('data.color.id', $color->id);

        $this->assertDatabaseHas('cms.firearms', [
            'color_id' => $color->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson('/firearms', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['manufacturer', 'model', 'label', 'type']);
    }

    public function test_store_rejects_an_invalid_firearm_type(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson('/firearms', [
                'manufacturer' => 'Daniel Defense',
                'model' => 'DDM4',
                'label' => 'DDM4',
                'type' => 'carbine',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('type');
    }

    public function test_index_can_filter_firearms_by_customizer(): void
    {
        $matchingFirearm = Firearm::factory()->recycle($this->user)->create([
            'customizer' => 'Langdon Tactical',
            'custom_package' => 'LTT Trigger Job',
        ]);
        Firearm::factory()->recycle($this->user)->create([
            'customizer' => 'Wilson Combat',
        ]);

        $this->actingAs($this->user, 'api')
            ->getJson('/firearms?filter[customizer]=Langdon')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $matchingFirearm->id)
            ->assertJsonPath('data.0.custom_package', 'LTT Trigger Job');
    }

    // show

    public function test_show_requires_authentication(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $this->getJson("/firearms/{$firearm->id}")->assertUnauthorized();
    }

    public function test_show_returns_firearm(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/firearms/{$firearm->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $firearm->id);
    }

    public function test_show_returns_404_for_another_users_firearm(): void
    {
        $other = Firearm::factory()->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/firearms/{$other->id}")
            ->assertNotFound();
    }

    public function test_show_returns_mounted_accessories_with_descriptors(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();

        Suppressor::factory()->recycle($this->user)->create([
            'firearm_id' => $firearm->id,
            'label' => 'Omega 9K',
            'is_nfa' => true,
        ]);
        Optic::factory()->recycle($this->user)->create([
            'firearm_id' => $firearm->id,
            'label' => 'Holosun 507c',
            'optic_type' => 'red_dot',
        ]);
        Light::factory()->recycle($this->user)->create([
            'firearm_id' => $firearm->id,
            'label' => 'Streamlight TLR-7',
            'lumens' => 500,
        ]);

        $accessories = $this->actingAs($this->user, 'api')
            ->getJson("/firearms/{$firearm->id}")
            ->assertOk()
            ->json('data.mounted_accessories');

        $byType = collect($accessories)->keyBy('type');

        $this->assertSame('Suppressor', $byType['Suppressor']['subtitle']);
        $this->assertTrue($byType['Suppressor']['is_nfa']);

        $this->assertSame('Red dot optic', $byType['Optic']['subtitle']);
        $this->assertFalse($byType['Optic']['is_nfa']);

        $this->assertSame('Weapon light · 500 lm', $byType['Light']['subtitle']);
    }

    public function test_show_returns_compatible_magazines_count(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();

        $firearm->magazines()->attach(
            Magazine::factory()->recycle($this->user)->count(2)->create()->pluck('id')
        );

        $this->actingAs($this->user, 'api')
            ->getJson("/firearms/{$firearm->id}")
            ->assertOk()
            ->assertJsonPath('data.compatible_magazines_count', 2)
            ->assertJsonPath('data.compatible_magazines_context.compatible_firearm_id', $firearm->id);
    }

    public function test_show_returns_magazines_currently_inserted_in_firearm(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $magazine = Magazine::factory()->recycle($this->user)->create([
            'current_firearm_id' => $firearm->id,
            'id_marking' => 'G19-01',
            'loaded_rounds' => 12,
        ]);

        $this->actingAs($this->user, 'api')
            ->getJson("/firearms/{$firearm->id}")
            ->assertOk()
            ->assertJsonPath('data.current_magazines.0.id', $magazine->id)
            ->assertJsonPath('data.current_magazines.0.id_marking', 'G19-01')
            ->assertJsonPath('data.current_magazines.0.loaded_rounds', 12);
    }

    // update

    public function test_update_requires_authentication(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $this->putJson("/firearms/{$firearm->id}", [])->assertUnauthorized();
    }

    public function test_update_modifies_firearm(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->putJson("/firearms/{$firearm->id}", [
                'manufacturer' => 'Beretta',
                'model' => '1301 Tactical C Gray 7+1',
                'customizer' => 'Langdon Tactical',
                'custom_package' => 'LTT Trigger Job',
                'label' => 'My 1301',
            ])
            ->assertOk()
            ->assertJsonPath('data.manufacturer', 'Beretta')
            ->assertJsonPath('data.customizer', 'Langdon Tactical')
            ->assertJsonPath('data.custom_package', 'LTT Trigger Job');

        $this->assertDatabaseHas('cms.firearms', [
            'id' => $firearm->id,
            'customizer' => 'Langdon Tactical',
            'custom_package' => 'LTT Trigger Job',
        ]);
    }

    public function test_update_sets_a_user_color(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $color = Color::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->putJson("/firearms/{$firearm->id}", ['color_id' => $color->id])
            ->assertOk()
            ->assertJsonPath('data.color.id', $color->id);

        $this->assertDatabaseHas('cms.firearms', [
            'id' => $firearm->id,
            'color_id' => $color->id,
        ]);
    }

    public function test_update_returns_404_for_another_users_firearm(): void
    {
        $other = Firearm::factory()->create();

        $this->actingAs($this->user, 'api')
            ->putJson("/firearms/{$other->id}", ['manufacturer' => 'X', 'model' => 'Y', 'label' => 'Z'])
            ->assertNotFound();
    }

    // destroy

    public function test_destroy_requires_authentication(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $this->deleteJson("/firearms/{$firearm->id}")->assertUnauthorized();
    }

    public function test_destroy_deletes_firearm(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create([
            'archived_at' => now(),
            'archive_reason' => 'other',
        ]);

        $this->actingAs($this->user, 'api')
            ->deleteJson("/firearms/{$firearm->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('cms.firearms', ['id' => $firearm->id]);
    }

    public function test_destroy_returns_404_for_another_users_firearm(): void
    {
        $other = Firearm::factory()->create();

        $this->actingAs($this->user, 'api')
            ->deleteJson("/firearms/{$other->id}")
            ->assertNotFound();
    }

    // notes

    public function test_note_index_returns_empty_array(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/firearms/{$firearm->id}/notes")
            ->assertOk()
            ->assertJsonPath('data', []);
    }

    public function test_note_store_creates_note(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->postJson("/firearms/{$firearm->id}/notes", ['note' => 'Cleaned today'])
            ->assertCreated()
            ->assertJsonPath('data.note', 'Cleaned today');

        $this->assertDatabaseHas('cms.notes', [
            'note' => 'Cleaned today',
            'notable_id' => $firearm->id,
            'notable_type' => Firearm::class,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_note_index_requires_authentication(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $this->getJson("/firearms/{$firearm->id}/notes")->assertUnauthorized();
    }

    public function test_note_store_requires_authentication(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $this->postJson("/firearms/{$firearm->id}/notes", ['note' => 'test'])->assertUnauthorized();
    }
}
