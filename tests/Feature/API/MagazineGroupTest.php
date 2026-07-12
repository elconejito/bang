<?php

namespace Tests\Feature\API;

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Firearm;
use App\Models\Location;
use App\Models\Magazine;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class MagazineGroupTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_group_endpoints_require_authentication(): void
    {
        $this->getJson('/magazine-groups')->assertUnauthorized();
        $this->getJson('/magazine-groups/999999/magazines')->assertUnauthorized();
    }

    public function test_group_index_returns_aggregate_summaries_without_individual_rows(): void
    {
        $caliber = Caliber::factory()->recycle($this->user)->create();
        $location = Location::factory()->recycle($this->user)->create(['label' => 'Large Safe']);
        $magazines = Magazine::factory()->recycle($this->user)->count(3)->sequence(
            ['manufacturer' => 'Glock', 'model_name' => 'OEM', 'capacity' => 17, 'location_id' => $location->id, 'loaded_rounds' => 0],
            ['manufacturer' => ' glock ', 'model_name' => 'oem', 'capacity' => 17, 'location_id' => $location->id, 'loaded_rounds' => 5],
            ['manufacturer' => 'GLOCK', 'model_name' => 'OEM', 'capacity' => 17, 'loaded_rounds' => 0],
        )->create();
        $magazines->each(fn (Magazine $magazine): mixed => $magazine->calibers()->attach($caliber));

        $response = $this->actingAs($this->user, 'api')->getJson('/magazine-groups')->assertOk();

        $response->assertJsonPath('meta.groups', 1)
            ->assertJsonPath('meta.magazines', 3)
            ->assertJsonPath('data.0.key', $magazines->min('id'))
            ->assertJsonPath('data.0.summary.total', 3)
            ->assertJsonPath('data.0.summary.loaded', 1)
            ->assertJsonPath('data.0.summary.empty', 2)
            ->assertJsonPath('data.0.locations.0.count', 2)
            ->assertJsonMissingPath('data.0.magazines');
    }

    public function test_group_index_applies_compatibility_search_and_caliber_filters_before_grouping(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $caliber = Caliber::factory()->recycle($this->user)->create();
        $matching = Magazine::factory()->recycle($this->user)->create(['manufacturer' => 'Magpul']);
        $matching->calibers()->attach($caliber);
        $matching->compatibleFirearms()->attach($firearm);
        Magazine::factory()->recycle($this->user)->create(['manufacturer' => 'Other']);

        $this->actingAs($this->user, 'api')->getJson('/magazine-groups?filter[compatible_firearm_id]='.$firearm->id.'&filter[caliber_id]='.$caliber->id.'&filter[search]=magp')
            ->assertOk()->assertJsonPath('meta.groups', 1)->assertJsonPath('data.0.summary.total', 1);
    }

    public function test_individual_endpoint_filters_sorts_and_paginates_with_context(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create(['manufacturer' => 'Glock', 'label' => '19']);
        $location = Location::factory()->recycle($this->user)->create(['label' => 'Safe']);
        $ammunition = Ammunition::factory()->recycle($this->user)->create(['manufacturer' => 'Federal', 'label' => 'HST']);
        $first = Magazine::factory()->recycle($this->user)->create(['manufacturer' => 'Glock', 'model_name' => 'OEM', 'capacity' => 17, 'id_marking' => 'B', 'location_id' => $location->id, 'loaded_rounds' => 5, 'loaded_ammunition_id' => $ammunition->id]);
        $second = Magazine::factory()->recycle($this->user)->create(['manufacturer' => 'Glock', 'model_name' => 'OEM', 'capacity' => 17, 'id_marking' => 'A', 'location_id' => $location->id, 'loaded_rounds' => 1, 'loaded_ammunition_id' => $ammunition->id]);
        foreach ([$first, $second] as $magazine) {
            $magazine->compatibleFirearms()->attach($firearm);
        }
        $groupId = $second->id;

        $this->actingAs($this->user, 'api')->getJson("/magazine-groups/{$groupId}/magazines?filter[compatible_firearm_id]={$firearm->id}&filter[state]=loaded&filter[location_id]={$location->id}&sort=-id_marking&per_page=1")
            ->assertOk()
            ->assertJsonPath('data.0.id', $first->id)
            ->assertJsonPath('data.0.loaded_rounds', 5)
            ->assertJsonPath('context.compatible_firearm.id', $firearm->id)
            ->assertJsonPath('group.key', min($first->id, $second->id))
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('meta.per_page', 1)
            ->assertJsonPath('meta.last_page', 2);
    }

    public function test_individual_endpoint_clamps_page_size_and_rejects_foreign_filters_and_bad_keys(): void
    {
        $magazine = Magazine::factory()->recycle($this->user)->create(['manufacturer' => 'Glock', 'capacity' => 17]);
        $this->actingAs($this->user, 'api')->getJson("/magazine-groups/{$magazine->id}/magazines?per_page=500")
            ->assertOk()->assertJsonPath('meta.per_page', 100);
        $this->actingAs($this->user, 'api')->getJson('/magazine-groups?filter[compatible_firearm_id]='.Firearm::factory()->create()->id)
            ->assertUnprocessable()->assertJsonValidationErrors('filter.compatible_firearm_id');
        $this->actingAs($this->user, 'api')->getJson('/magazine-groups/999999/magazines')->assertNotFound();
        $this->actingAs($this->user, 'api')->getJson('/magazine-groups/'.Magazine::factory()->create()->id.'/magazines')->assertNotFound();
    }
}
