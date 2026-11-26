<?php

namespace Tests\Feature\Queries;

use App\Data\Magazines\MagazineGroupKey;
use App\Models\Caliber;
use App\Models\Firearm;
use App\Models\Magazine;
use App\Models\User;
use App\Queries\Magazines\MagazineGroupQuery;
use App\Queries\Magazines\MagazinesInGroupQuery;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class MagazineGroupQueryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_it_groups_normalized_facts_and_keeps_different_caliber_sets_separate(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $nineMillimeter = Caliber::factory()->recycle($user)->create();
        $forty = Caliber::factory()->recycle($user)->create();

        $first = Magazine::factory()->recycle($user)->create([
            'manufacturer' => '  Glock ',
            'model_name' => ' OEM ',
            'capacity' => 17,
        ]);
        $second = Magazine::factory()->recycle($user)->create([
            'manufacturer' => 'glock',
            'model_name' => 'oem',
            'capacity' => 17,
        ]);
        $differentCalibers = Magazine::factory()->recycle($user)->create([
            'manufacturer' => 'GLOCK',
            'model_name' => 'OEM',
            'capacity' => 17,
        ]);
        $first->calibers()->attach($nineMillimeter);
        $second->calibers()->attach($nineMillimeter);
        $differentCalibers->calibers()->attach([$nineMillimeter->id, $forty->id]);

        $groups = app(MagazineGroupQuery::class)->get($user);

        $this->assertCount(2, $groups);
        $this->assertEqualsCanonicalizing(
            [$first->id, $second->id],
            $groups->first(fn (array $group): bool => $group['magazines']->count() === 2)['magazines']->modelKeys(),
        );
    }

    public function test_it_filters_groups_by_compatible_firearm_before_grouping(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $firearm = Firearm::factory()->recycle($user)->create();
        $compatible = Magazine::factory()->recycle($user)->create();
        Magazine::factory()->recycle($user)->create([
            'manufacturer' => $compatible->manufacturer,
            'model_name' => $compatible->model_name,
            'capacity' => $compatible->capacity,
        ]);
        $compatible->compatibleFirearms()->attach($firearm);

        $groups = app(MagazineGroupQuery::class)->get($user, $firearm);

        $this->assertCount(1, $groups);
        $this->assertSame([$compatible->id], $groups->sole()['magazines']->modelKeys());
    }

    public function test_it_resolves_only_the_exact_encoded_group_for_the_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($user);
        $caliber = Caliber::factory()->recycle($user)->create();
        $matching = Magazine::factory()->recycle($user)->create([
            'manufacturer' => 'Smith   & Wesson',
            'model_name' => 'M&P',
            'capacity' => 15,
        ]);
        $matching->calibers()->attach($caliber);
        Magazine::factory()->recycle($user)->create([
            'manufacturer' => 'Smith & Wesson',
            'model_name' => 'M&P',
            'capacity' => 17,
        ])->calibers()->attach($caliber);
        Magazine::factory()->recycle($otherUser)->create([
            'manufacturer' => 'Smith & Wesson',
            'model_name' => 'M&P',
            'capacity' => 15,
        ]);

        $key = MagazineGroupKey::make('smith & wesson', 'm&p', 15, [$caliber->id]);
        $results = app(MagazinesInGroupQuery::class)->builder($user, $key)->get();

        $this->assertSame([$matching->id], $results->modelKeys());
    }

    public function test_it_sorts_manufacturers_and_numeric_model_names_as_a_catalog(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        foreach ([
            ['manufacturer' => 'Magpul', 'model_name' => 'PMAG 17 GL9', 'capacity' => 17],
            ['manufacturer' => 'Glock', 'model_name' => 'Glock 33rnd OEM', 'capacity' => 33],
            ['manufacturer' => 'Glock', 'model_name' => 'Glock 6rnd OEM +1', 'capacity' => 7],
            ['manufacturer' => 'Glock', 'model_name' => 'Glock 6rnd OEM +3', 'capacity' => 9],
            ['manufacturer' => 'Glock', 'model_name' => 'Glock 15rnd OEM', 'capacity' => 15],
            ['manufacturer' => 'Glock', 'model_name' => 'Glock 17rnd OEM', 'capacity' => 17],
        ] as $attributes) {
            Magazine::factory()->recycle($user)->create($attributes);
        }

        $groups = app(MagazineGroupQuery::class)->get($user);

        $this->assertSame([
            'Glock 6rnd OEM +1',
            'Glock 6rnd OEM +3',
            'Glock 15rnd OEM',
            'Glock 17rnd OEM',
            'Glock 33rnd OEM',
            'PMAG 17 GL9',
        ], $groups->map(fn (array $group): ?string => $group['magazines']->first()->model_name)->all());
    }
}
