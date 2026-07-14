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
use App\Models\Picture;
use App\Models\Range;
use App\Models\SessionLine;
use App\Models\Store;
use App\Models\Suppressor;
use App\Models\Target;
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
            NotableType::Picture => $this->createPicture(),
            NotableType::Range => Range::query()->create([
                'label' => 'Notes Test Range',
                'user_id' => $this->user->id,
            ]),
            NotableType::SessionLine => SessionLine::factory()->recycle($this->user)->create(),
            NotableType::Store => Store::factory()->recycle($this->user)->create(),
            NotableType::Suppressor => Suppressor::factory()->recycle($this->user)->create(),
            NotableType::Target => $this->createTarget(),
            NotableType::TrainingSession => TrainingSession::factory()->recycle($this->user)->create(),
        };
    }

    private function createPicture(): Picture
    {
        return Picture::query()->create([
            'name' => 'Notes Test Picture',
            'filename' => 'notes-test.jpg',
            'user_id' => $this->user->id,
        ]);
    }

    private function createTarget(): Target
    {
        $trainingSession = TrainingSession::factory()->recycle($this->user)->create();
        $picture = $this->createPicture();

        return Target::query()->forceCreate([
            'label' => 'Notes Test Target',
            'distance' => 25,
            'group_size' => 2.5,
            'picture_id' => $picture->id,
            'training_session_id' => $trainingSession->id,
            'user_id' => $this->user->id,
        ]);
    }
}
