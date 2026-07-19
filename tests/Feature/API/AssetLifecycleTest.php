<?php

namespace Tests\Feature\API;

use App\Models\ActivityEvent;
use App\Models\Ammunition;
use App\Models\Firearm;
use App\Models\Light;
use App\Models\Magazine;
use App\Models\MiscAccessory;
use App\Models\Optic;
use App\Models\SessionLine;
use App\Models\Suppressor;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AssetLifecycleTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_accessories_archive_unmount_filter_and_transform_consistently(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $cases = [
            [Suppressor::class, 'suppressors'],
            [Optic::class, 'optics'],
            [Light::class, 'lights'],
            [MiscAccessory::class, 'misc-accessories'],
        ];

        foreach ($cases as [$modelClass, $route]) {
            $asset = $modelClass::factory()->recycle($this->user)->create(['firearm_id' => $firearm->id]);
            $payload = ['reason' => 'sold', 'description' => 'Lifecycle test'];

            $this->actingAs($this->user, 'api')->postJson("/{$route}/{$asset->id}/archive", $payload)
                ->assertOk()
                ->assertJsonPath('data.status', 'archived')
                ->assertJsonPath('data.archive_reason', 'sold')
                ->assertJsonPath('data.archive_description', 'Lifecycle test')
                ->assertJsonPath('data.firearm_id', null);

            $this->actingAs($this->user, 'api')->postJson("/{$route}/{$asset->id}/archive", $payload)->assertOk();
            $this->assertSame(1, ActivityEvent::query()->where('subject_type', $modelClass)->where('subject_id', $asset->id)->where('type', 'ARCHIVED')->count());
            $this->actingAs($this->user, 'api')->getJson("/{$route}")->assertOk()->assertJsonCount(0, 'data');
            $this->actingAs($this->user, 'api')->getJson("/{$route}?filter[status]=archived")->assertOk()->assertJsonCount(1, 'data');
        }
    }

    public function test_unarchive_is_idempotent_and_records_each_real_transition(): void
    {
        $optic = Optic::factory()->recycle($this->user)->create();
        $this->actingAs($this->user, 'api')->postJson("/optics/{$optic->id}/archive", ['reason' => 'repair'])->assertOk();
        $this->actingAs($this->user, 'api')->postJson("/optics/{$optic->id}/unarchive")->assertOk()->assertJsonPath('data.status', 'active');
        $this->actingAs($this->user, 'api')->postJson("/optics/{$optic->id}/unarchive")->assertOk();

        $this->assertSame(['ADDED', 'ARCHIVED', 'UNARCHIVED'], ActivityEvent::query()->where('subject_type', Optic::class)->where('subject_id', $optic->id)->orderBy('id')->pluck('type')->all());
    }

    public function test_magazine_archive_preserves_load_and_physical_status(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $ammunition = Ammunition::factory()->recycle($this->user)->create();
        $magazine = Magazine::factory()->recycle($this->user)->create([
            'current_firearm_id' => $firearm->id,
            'loaded_ammunition_id' => $ammunition->id,
            'loaded_rounds' => 7,
        ]);

        $this->actingAs($this->user, 'api')->postJson("/magazines/{$magazine->id}/archive", ['reason' => 'retired'])
            ->assertOk()
            ->assertJsonPath('data.lifecycle_status', 'archived')
            ->assertJsonPath('data.status', 'loaded')
            ->assertJsonPath('data.display_status', 'loaded')
            ->assertJsonPath('data.loaded_rounds', 7)
            ->assertJsonPath('data.loaded_ammunition_id', $ammunition->id)
            ->assertJsonPath('data.current_firearm', null);

        $this->actingAs($this->user, 'api')->getJson('/magazines')->assertOk()->assertJsonCount(0, 'data');
        $this->actingAs($this->user, 'api')->getJson('/magazines?filter[lifecycle_status]=archived')->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_delete_requires_archive_and_blocks_meaningful_history(): void
    {
        $active = Optic::factory()->recycle($this->user)->create();
        $this->actingAs($this->user, 'api')->deleteJson("/optics/{$active->id}")->assertConflict()->assertJsonPath('blockers.0.type', 'active');

        $unused = Optic::factory()->recycle($this->user)->create();
        $this->actingAs($this->user, 'api')->postJson("/optics/{$unused->id}/archive", ['reason' => 'other'])->assertOk();
        $this->actingAs($this->user, 'api')->deleteJson("/optics/{$unused->id}")->assertNoContent();
        $this->assertModelMissing($unused);

        $suppressor = Suppressor::factory()->recycle($this->user)->create(['archived_at' => now(), 'archive_reason' => 'retired']);
        SessionLine::factory()->recycle($this->user)->create(['suppressor_id' => $suppressor->id]);
        $this->actingAs($this->user, 'api')->deleteJson("/suppressors/{$suppressor->id}")->assertConflict()->assertJsonFragment(['type' => 'training_history', 'count' => 1]);
    }

    public function test_archived_items_reject_new_activity_and_assignments(): void
    {
        $archivedFirearm = Firearm::factory()->recycle($this->user)->create(['archived_at' => now(), 'archive_reason' => 'retired']);
        $this->actingAs($this->user, 'api')->postJson('/optics', ['manufacturer' => 'Aimpoint', 'label' => 'T2', 'firearm_id' => $archivedFirearm->id])
            ->assertUnprocessable()->assertJsonValidationErrors('firearm_id');

        $activeFirearm = Firearm::factory()->recycle($this->user)->create();
        $optic = Optic::factory()->recycle($this->user)->create(['archived_at' => now(), 'archive_reason' => 'retired']);
        $this->actingAs($this->user, 'api')->putJson("/optics/{$optic->id}", ['firearm_id' => $activeFirearm->id])
            ->assertConflict()->assertJsonPath('code', 'archived_item_assignment_blocked');
        $this->actingAs($this->user, 'api')->postJson("/optics/{$optic->id}/events", ['event_type' => 'CLEAN', 'event_date' => now()->toDateString()])
            ->assertConflict()->assertJsonPath('code', 'archived_item_activity_blocked');
    }

    public function test_archived_magazine_rejects_state_changes(): void
    {
        $magazine = Magazine::factory()->recycle($this->user)->create(['archived_at' => now(), 'archive_reason' => 'retired']);
        $response = $this->actingAs($this->user, 'api')->patchJson("/magazines/{$magazine->id}/state", [
            'location_id' => null,
            'current_firearm_id' => null,
            'loaded_ammunition_id' => null,
            'loaded_rounds' => 0,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('magazine');
    }

    public function test_lifecycle_events_render_in_activity_history(): void
    {
        $light = Light::factory()->recycle($this->user)->create();
        $this->actingAs($this->user, 'api')->postJson("/lights/{$light->id}/archive", ['reason' => 'broken', 'description' => 'Switch failed'])->assertOk();

        $this->actingAs($this->user, 'api')->getJson("/lights/{$light->id}/events?filter[group]=lifecycle")
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.type', 'ARCHIVED')->assertJsonPath('data.0.title', 'Archived');
    }

    public function test_grouped_and_combined_accessories_default_active_and_allow_archived(): void
    {
        Magazine::factory()->recycle($this->user)->create();
        $archived = Magazine::factory()->recycle($this->user)->create(['archived_at' => now(), 'archive_reason' => 'retired']);
        Optic::factory()->recycle($this->user)->create(['archived_at' => now(), 'archive_reason' => 'broken']);

        $this->actingAs($this->user, 'api')->getJson('/magazine-groups')->assertOk()->assertJsonPath('meta.magazines', 1);
        $this->actingAs($this->user, 'api')->getJson('/magazine-groups?filter[lifecycle_status]=archived')->assertOk()->assertJsonPath('meta.magazines', 1);
        $this->actingAs($this->user, 'api')->getJson("/magazine-groups/{$archived->id}/magazines?filter[lifecycle_status]=archived")
            ->assertOk()->assertJsonPath('data.0.lifecycle_status', 'archived')->assertJsonPath('data.0.archive_reason', 'retired');
        $this->actingAs($this->user, 'api')->getJson('/accessories')->assertOk()->assertJsonCount(0, 'data.optics');
        $this->actingAs($this->user, 'api')->getJson('/accessories?filter[lifecycle_status]=archived')->assertOk()->assertJsonCount(1, 'data.optics');
    }
}
