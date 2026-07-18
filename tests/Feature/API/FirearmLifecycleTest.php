<?php

namespace Tests\Feature\API;

use App\Models\ActivityEvent;
use App\Models\Firearm;
use App\Models\Light;
use App\Models\Optic;
use App\Models\SessionLine;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class FirearmLifecycleTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_archive_requires_authentication_and_valid_reason(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();

        $this->postJson("/firearms/{$firearm->id}/archive", ['reason' => 'sold'])->assertUnauthorized();
        $this->actingAs($this->user, 'api')
            ->postJson("/firearms/{$firearm->id}/archive", ['reason' => 'invalid'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('reason');
    }

    public function test_archive_and_unarchive_reject_another_users_firearm(): void
    {
        $otherFirearm = Firearm::factory()->create();

        $this->actingAs($this->user, 'api')
            ->postJson("/firearms/{$otherFirearm->id}/archive", ['reason' => 'sold'])
            ->assertNotFound();

        $otherFirearm->update([
            'archived_at' => now(),
            'archive_reason' => 'sold',
        ]);

        $this->actingAs($this->user, 'api')
            ->postJson("/firearms/{$otherFirearm->id}/unarchive")
            ->assertNotFound();
    }

    public function test_archive_records_state_and_one_immutable_activity_event(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $payload = ['reason' => 'sold', 'description' => 'Sold to a friend.'];

        $this->actingAs($this->user, 'api')
            ->postJson("/firearms/{$firearm->id}/archive", $payload)
            ->assertOk()
            ->assertJsonPath('data.status', 'archived')
            ->assertJsonPath('data.archive_reason', 'sold')
            ->assertJsonPath('data.archive_description', 'Sold to a friend.');

        $this->actingAs($this->user, 'api')
            ->postJson("/firearms/{$firearm->id}/archive", $payload)
            ->assertOk();

        $this->assertSame(1, ActivityEvent::query()->where('subject_type', Firearm::class)->where('subject_id', $firearm->id)->where('type', 'ARCHIVED')->count());
    }

    public function test_unarchive_clears_current_fields_and_each_real_transition_is_recorded(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')->postJson("/firearms/{$firearm->id}/archive", ['reason' => 'sold'])->assertOk();
        $this->actingAs($this->user, 'api')->postJson("/firearms/{$firearm->id}/unarchive")->assertOk()->assertJsonPath('data.status', 'active');
        $this->actingAs($this->user, 'api')->postJson("/firearms/{$firearm->id}/unarchive")->assertOk();
        $this->actingAs($this->user, 'api')->postJson("/firearms/{$firearm->id}/archive", ['reason' => 'transferred'])->assertOk();

        $events = ActivityEvent::query()->where('subject_type', Firearm::class)->where('subject_id', $firearm->id)->orderBy('id')->pluck('type')->all();
        $this->assertSame(['ARCHIVED', 'UNARCHIVED', 'ARCHIVED'], $events);
    }

    public function test_archive_can_unmount_selected_or_all_accessories(): void
    {
        $selectedFirearm = Firearm::factory()->recycle($this->user)->create();
        $selected = Optic::factory()->recycle($this->user)->create(['firearm_id' => $selectedFirearm->id]);
        $retained = Light::factory()->recycle($this->user)->create(['firearm_id' => $selectedFirearm->id]);

        $this->actingAs($this->user, 'api')->postJson("/firearms/{$selectedFirearm->id}/archive", [
            'reason' => 'repair',
            'unmount_accessories' => [['type' => 'optic', 'id' => $selected->id]],
        ])->assertOk();

        $this->assertNull($selected->refresh()->firearm_id);
        $this->assertSame($selectedFirearm->id, $retained->refresh()->firearm_id);

        $allFirearm = Firearm::factory()->recycle($this->user)->create();
        $optic = Optic::factory()->recycle($this->user)->create(['firearm_id' => $allFirearm->id]);
        $light = Light::factory()->recycle($this->user)->create(['firearm_id' => $allFirearm->id]);
        $this->actingAs($this->user, 'api')->postJson("/firearms/{$allFirearm->id}/archive", ['reason' => 'sold', 'unmount_all_accessories' => true])->assertOk();

        $this->assertNull($optic->refresh()->firearm_id);
        $this->assertNull($light->refresh()->firearm_id);
    }

    public function test_index_defaults_active_and_can_filter_archived_or_all(): void
    {
        $active = Firearm::factory()->recycle($this->user)->create();
        $archived = Firearm::factory()->recycle($this->user)->create(['archived_at' => now(), 'archive_reason' => 'retired']);

        $this->actingAs($this->user, 'api')->getJson('/firearms')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $active->id);
        $this->actingAs($this->user, 'api')->getJson('/firearms?filter[status]=archived')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $archived->id);
        $this->actingAs($this->user, 'api')->getJson('/firearms?filter[status]=all')->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_delete_requires_archived_state(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')->deleteJson("/firearms/{$firearm->id}")
            ->assertConflict()
            ->assertJsonPath('code', 'firearm_delete_blocked')
            ->assertJsonPath('blockers.0.type', 'active');
        $this->assertModelExists($firearm);
    }

    public function test_archived_firearm_with_only_lifecycle_events_can_be_deleted(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $this->actingAs($this->user, 'api')->postJson("/firearms/{$firearm->id}/archive", ['reason' => 'other'])->assertOk();

        $this->actingAs($this->user, 'api')->deleteJson("/firearms/{$firearm->id}")->assertNoContent();

        $this->assertModelMissing($firearm);
        $this->assertSame(0, ActivityEvent::withoutGlobalScopes()->where('subject_type', Firearm::class)->where('subject_id', $firearm->id)->count());
    }

    public function test_historical_training_blocks_deletion(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create(['archived_at' => now(), 'archive_reason' => 'retired']);
        SessionLine::factory()->recycle($this->user)->create(['firearm_id' => $firearm->id]);

        $this->actingAs($this->user, 'api')->deleteJson("/firearms/{$firearm->id}")
            ->assertConflict()
            ->assertJsonFragment(['type' => 'training_history', 'count' => 1]);
    }

    public function test_activity_feed_includes_lifecycle_events(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $this->actingAs($this->user, 'api')->postJson("/firearms/{$firearm->id}/archive", ['reason' => 'broken', 'description' => 'Cracked frame'])->assertOk();

        $this->actingAs($this->user, 'api')->getJson("/firearms/{$firearm->id}/activity?filter[type]=ARCHIVED")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.type', 'ARCHIVED')
            ->assertJsonPath('data.0.title', 'Archived');
    }
}
