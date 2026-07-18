<?php

namespace Tests\Feature\API;

use App\Models\ActivityEvent;
use App\Models\Firearm;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class FirearmActivityEventTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Firearm $firearm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->firearm = Firearm::factory()->recycle($this->user)->create();
    }

    public function test_manual_firearm_events_require_authentication(): void
    {
        $this->postJson("/firearms/{$this->firearm->id}/activity", [
            'event_type' => 'CLEAN',
            'event_date' => '2026-07-18',
        ])->assertUnauthorized();
    }

    public function test_owner_can_log_cleaning_and_repair_events(): void
    {
        foreach ([['CLEAN', '2026-07-12', 'Field stripped'], ['REPAIR', '2026-07-13', 'Replaced extractor']] as [$type, $date, $description]) {
            $this->actingAs($this->user, 'api')
                ->postJson("/firearms/{$this->firearm->id}/activity", [
                    'event_type' => $type,
                    'event_date' => $date,
                    'description' => $description,
                ])
                ->assertCreated()
                ->assertJsonPath('data.type', $type)
                ->assertJsonPath('data.date', $date);
        }

        $this->assertSame(2, ActivityEvent::query()
            ->where('subject_type', $this->firearm->getMorphClass())
            ->where('subject_id', $this->firearm->id)
            ->whereIn('type', ['CLEAN', 'REPAIR'])
            ->count());

        $this->actingAs($this->user, 'api')
            ->getJson("/firearms/{$this->firearm->id}/activity?filter[type]=REPAIR")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.type', 'REPAIR')
            ->assertJsonPath('data.0.subtitle', 'Replaced extractor');
    }

    public function test_manual_endpoint_rejects_automatic_event_types(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson("/firearms/{$this->firearm->id}/activity", [
                'event_type' => 'MOUNT',
                'event_date' => '2026-07-18',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('event_type');
    }

    public function test_manual_events_cannot_be_created_for_another_users_firearm_or_an_archived_firearm(): void
    {
        $otherFirearm = Firearm::factory()->create();

        $this->actingAs($this->user, 'api')
            ->postJson("/firearms/{$otherFirearm->id}/activity", [
                'event_type' => 'CLEAN',
                'event_date' => '2026-07-18',
            ])
            ->assertNotFound();

        $this->firearm->forceFill(['archived_at' => now()])->save();

        $this->actingAs($this->user, 'api')
            ->postJson("/firearms/{$this->firearm->id}/activity", [
                'event_type' => 'CLEAN',
                'event_date' => '2026-07-18',
            ])
            ->assertConflict()
            ->assertJsonPath('code', 'archived_item_activity_blocked');
    }
}
