<?php

namespace Tests\Feature\API;

use App\Enums\NotableType;
use App\Models\Ammunition;
use App\Models\Firearm;
use App\Models\Light;
use App\Models\Location;
use App\Models\Magazine;
use App\Models\MiscAccessory;
use App\Models\Note;
use App\Models\Optic;
use App\Models\Order;
use App\Models\Range;
use App\Models\SessionLine;
use App\Models\Store;
use App\Models\Suppressor;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class NoteTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_every_supported_model_can_create_and_list_notes(): void
    {
        $this->actingAs($this->user, 'api');

        foreach (NotableType::cases() as $notableType) {
            $notable = $this->createNotable($notableType);

            $this->postJson("/{$notableType->value}/{$notable->getKey()}/notes", [
                'note' => "Note for {$notableType->value}",
            ])->assertCreated()
                ->assertJsonPath('data.note', "Note for {$notableType->value}");

            $this->getJson("/{$notableType->value}/{$notable->getKey()}/notes")
                ->assertOk()
                ->assertJsonPath('data.0.note', "Note for {$notableType->value}");

            $this->assertDatabaseHas('cms.notes', [
                'user_id' => $this->user->id,
                'notable_type' => $notable->getMorphClass(),
                'notable_id' => $notable->getKey(),
            ]);
        }
    }

    public function test_notes_are_newest_first_searchable_paginated_and_isolated(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $otherFirearm = Firearm::factory()->recycle($this->user)->create();
        $ammunition = Ammunition::factory()->recycle($this->user)->create();

        Note::factory()->recycle($this->user)->create([
            'note' => 'Old needle note',
            'notable_type' => $firearm->getMorphClass(),
            'notable_id' => $firearm->id,
            'created_at' => now()->subDays(2),
        ]);
        Note::factory()->recycle($this->user)->create([
            'note' => 'Middle note',
            'notable_type' => $firearm->getMorphClass(),
            'notable_id' => $firearm->id,
            'created_at' => now()->subDay(),
        ]);
        Note::factory()->recycle($this->user)->create([
            'note' => 'Newest NEEDLE note',
            'notable_type' => $firearm->getMorphClass(),
            'notable_id' => $firearm->id,
            'created_at' => now(),
        ]);
        Note::factory()->recycle($this->user)->create([
            'note' => 'Different firearm',
            'notable_type' => $otherFirearm->getMorphClass(),
            'notable_id' => $otherFirearm->id,
        ]);
        Note::factory()->recycle($this->user)->create([
            'note' => 'Different model with a potentially overlapping ID',
            'notable_type' => $ammunition->getMorphClass(),
            'notable_id' => $ammunition->id,
        ]);

        $this->actingAs($this->user, 'api')
            ->getJson("/firearms/{$firearm->id}/notes?per_page=2")
            ->assertOk()
            ->assertJsonPath('data.0.note', 'Newest NEEDLE note')
            ->assertJsonPath('data.1.note', 'Middle note')
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('meta.total', 3);

        $this->actingAs($this->user, 'api')
            ->getJson("/firearms/{$firearm->id}/notes?search=needle")
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.note', 'Newest NEEDLE note')
            ->assertJsonPath('data.1.note', 'Old needle note');
    }

    public function test_note_creation_is_validated_and_scoped_to_the_owner(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $otherUsersFirearm = Firearm::factory()->create();

        $this->actingAs($this->user, 'api')
            ->postJson("/firearms/{$firearm->id}/notes", [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('note');

        $this->actingAs($this->user, 'api')
            ->postJson("/firearms/{$firearm->id}/notes", ['note' => ['not', 'a', 'string']])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('note');

        $this->actingAs($this->user, 'api')
            ->postJson("/firearms/{$otherUsersFirearm->id}/notes", ['note' => 'Private'])
            ->assertNotFound();
    }

    public function test_note_routes_require_authentication_and_the_allowlist_excludes_unsupported_types(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();

        $this->getJson("/firearms/{$firearm->id}/notes")->assertUnauthorized();
        $this->postJson("/firearms/{$firearm->id}/notes", ['note' => 'Private'])
            ->assertUnauthorized();

        $this->assertNull(NotableType::tryFrom('users'));
    }

    public function test_notes_cannot_be_read_from_another_users_entity(): void
    {
        $otherUsersFirearm = Firearm::factory()->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/firearms/{$otherUsersFirearm->id}/notes")
            ->assertNotFound();
    }

    public function test_deleting_a_notable_deletes_its_notes(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $note = $firearm->notes()->create([
            'user_id' => $this->user->id,
            'note' => 'Delete with parent',
        ]);

        $firearm->delete();

        $this->assertDatabaseMissing('cms.notes', ['id' => $note->id]);
    }

    public function test_every_supported_model_can_update_and_delete_notes(): void
    {
        $this->actingAs($this->user, 'api');

        foreach (NotableType::cases() as $notableType) {
            $notable = $this->createNotable($notableType);
            $note = $notable->notes()->create([
                'user_id' => $this->user->id,
                'note' => 'Original note',
            ]);
            $createdAt = $note->refresh()->created_at->toISOString();
            $this->travel(1)->minutes();

            $this->patchJson("/{$notableType->value}/{$notable->getKey()}/notes/{$note->id}", [
                'note' => 'Edited note',
                'user_id' => 999999,
            ])->assertOk()
                ->assertJsonPath('data.note', 'Edited note')
                ->assertJsonPath('data.created_at', $createdAt);

            $note->refresh();
            $this->assertSame('Edited note', $note->note);
            $this->assertSame($this->user->id, $note->user_id);
            $this->assertTrue($note->updated_at->greaterThan($note->created_at));

            $this->deleteJson("/{$notableType->value}/{$notable->getKey()}/notes/{$note->id}")
                ->assertNoContent();
            $this->assertModelMissing($note);
            $this->travelBack();
        }
    }

    public function test_note_updates_are_validated(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $note = $firearm->notes()->create(['user_id' => $this->user->id, 'note' => 'Original']);
        $this->actingAs($this->user, 'api');

        foreach ([[], ['note' => ''], ['note' => ['invalid']], ['note' => str_repeat('a', 10001)]] as $payload) {
            $this->patchJson("/firearms/{$firearm->id}/notes/{$note->id}", $payload)
                ->assertUnprocessable()
                ->assertJsonValidationErrors('note');
        }

        $this->assertSame('Original', $note->fresh()->note);
    }

    public function test_note_mutations_are_scoped_to_the_parent_and_owner(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $otherFirearm = Firearm::factory()->recycle($this->user)->create();
        $foreignFirearm = Firearm::factory()->create();
        $ammunition = Ammunition::factory()->recycle($this->user)->create();
        $note = $firearm->notes()->create(['user_id' => $this->user->id, 'note' => 'Original']);
        $foreignNote = $foreignFirearm->notes()->create(['user_id' => $foreignFirearm->user_id, 'note' => 'Private']);
        $this->actingAs($this->user, 'api');

        foreach ([
            "/firearms/{$otherFirearm->id}/notes/{$note->id}",
            "/ammunition/{$ammunition->id}/notes/{$note->id}",
            "/firearms/{$foreignFirearm->id}/notes/{$foreignNote->id}",
            "/firearms/{$firearm->id}/notes/999999999",
        ] as $url) {
            $this->patchJson($url, ['note' => 'Changed'])->assertNotFound();
            $this->deleteJson($url)->assertNotFound();
        }

        $this->assertSame('Original', $note->fresh()->note);
        $this->assertModelExists($foreignNote);
    }

    public function test_note_mutations_require_authentication(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $note = $firearm->notes()->create(['user_id' => $this->user->id, 'note' => 'Original']);

        $this->patchJson("/firearms/{$firearm->id}/notes/{$note->id}", ['note' => 'Changed'])
            ->assertUnauthorized();
        $this->deleteJson("/firearms/{$firearm->id}/notes/{$note->id}")->assertUnauthorized();
        $this->assertSame('Original', $note->fresh()->note);
    }

    private function createNotable(NotableType $notableType): Model
    {
        return match ($notableType) {
            NotableType::Ammunition => Ammunition::factory()->recycle($this->user)->create(),
            NotableType::Firearm => Firearm::factory()->recycle($this->user)->create(),
            NotableType::Light => Light::factory()->recycle($this->user)->create(),
            NotableType::Location => Location::factory()->recycle($this->user)->create(),
            NotableType::Magazine => Magazine::factory()->recycle($this->user)->create(),
            NotableType::MiscAccessory => MiscAccessory::factory()->recycle($this->user)->create(),
            NotableType::Optic => Optic::factory()->recycle($this->user)->create(),
            NotableType::Order => Order::query()->create([
                'order_date' => now()->toDateString(),
                'rounds' => 0,
                'total_cost' => 0,
                'user_id' => $this->user->id,
            ]),
            NotableType::Range => Range::query()->create([
                'label' => 'Notes Test Range',
                'user_id' => $this->user->id,
            ]),
            NotableType::SessionLine => SessionLine::factory()->recycle($this->user)->create(),
            NotableType::Store => Store::factory()->recycle($this->user)->create(),
            NotableType::Suppressor => Suppressor::factory()->recycle($this->user)->create(),
            NotableType::TrainingSession => TrainingSession::factory()->recycle($this->user)->create(),
        };
    }
}
