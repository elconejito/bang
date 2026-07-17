<?php

namespace Tests\Feature\Models;

use App\Models\Firearm;
use App\Models\Location;
use App\Models\Magazine;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class MagazineTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_it_derives_display_and_load_states_from_placement_and_rounds(): void
    {
        $empty = Magazine::factory()->make(['loaded_rounds' => 0]);
        $loaded = Magazine::factory()->make(['loaded_rounds' => 12]);
        $insertedEmpty = Magazine::factory()->make(['current_firearm_id' => 1, 'loaded_rounds' => 0]);

        $this->assertSame('empty', $empty->display_status);
        $this->assertSame('empty', $empty->load_state);
        $this->assertSame('loaded', $loaded->display_status);
        $this->assertSame('loaded', $loaded->load_state);
        $this->assertSame('in_gun', $insertedEmpty->display_status);
        $this->assertSame('empty', $insertedEmpty->load_state);
    }

    public function test_it_exposes_storage_current_firearm_and_compatibility_relationships(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $location = Location::factory()->recycle($user)->create();
        $firearm = Firearm::factory()->recycle($user)->create();
        $storedMagazine = Magazine::factory()->recycle($user)->create([
            'location_id' => $location->id,
        ]);
        $insertedMagazine = Magazine::factory()->recycle($user)->create([
            'current_firearm_id' => $firearm->id,
        ]);
        $insertedMagazine->compatibleFirearms()->attach($firearm);

        $this->assertTrue($storedMagazine->location->is($location));
        $this->assertTrue($insertedMagazine->currentFirearm->is($firearm));
        $this->assertTrue($insertedMagazine->compatibleFirearms->contains($firearm));
        $this->assertTrue($location->magazines->contains($storedMagazine));
        $this->assertTrue($firearm->currentMagazines->contains($insertedMagazine));
    }

    public function test_state_and_placement_scopes_filter_magazines(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $location = Location::factory()->recycle($user)->create();
        $firearm = Firearm::factory()->recycle($user)->create();
        $stored = Magazine::factory()->recycle($user)->create(['location_id' => $location->id]);
        $inserted = Magazine::factory()->recycle($user)->create(['current_firearm_id' => $firearm->id]);
        $loaded = Magazine::factory()->recycle($user)->create(['loaded_rounds' => 5]);
        $inserted->compatibleFirearms()->attach($firearm);

        $this->assertTrue(Magazine::storedAt($location)->sole()->is($stored));
        $this->assertTrue(Magazine::inFirearm($firearm)->sole()->is($inserted));
        $this->assertTrue(Magazine::compatibleWithFirearm($firearm)->sole()->is($inserted));
        $this->assertTrue(Magazine::loaded()->sole()->is($loaded));
        $this->assertCount(2, Magazine::empty()->get());
    }

    public function test_duplicate_firearm_compatibility_is_rejected(): void
    {
        $user = User::factory()->create();
        $firearm = Firearm::factory()->recycle($user)->create();
        $magazine = Magazine::factory()->recycle($user)->create();
        $magazine->compatibleFirearms()->attach($firearm);

        $this->expectException(UniqueConstraintViolationException::class);

        $magazine->compatibleFirearms()->attach($firearm);
    }
}
