<?php

namespace Database\Seeders;

use App\Actions\Firearms\LogFirearmActivityEvent;
use App\Actions\Orders\SyncOrder;
use App\Enums\OrderAssetType;
use App\Enums\PictureProcessingStatus;
use App\Jobs\ProcessPictureDerivatives;
use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Firearm;
use App\Models\Location;
use App\Models\Magazine;
use App\Models\Picture;
use App\Models\Range;
use App\Models\Reference\Color;
use App\Models\Reference\Purpose;
use App\Models\Store;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use LogicException;

class FullInventorySeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            throw new LogicException('Full inventory fixtures require a local or testing environment.');
        }

        DB::transaction(function (): void {
            $user = User::where('email', config('app.test_user_email'))->lockForUpdate()->firstOrFail();
            $previousUser = Auth::user();
            Auth::setUser($user);
            try {
                if (Firearm::exists()) {
                    throw new LogicException('Use demo:seed full to rebuild the demo inventory.');
                }
                $this->populate($user);
            } finally {
                Auth::forgetGuards();
                if ($previousUser !== null) {
                    Auth::setUser($previousUser);
                }
            }
        });
    }

    private function populate(User $user): void
    {
        $owner = ['user_id' => $user->id];
        $archived = ['archived_at' => now()->subDay(), 'archive_reason' => 'retired'];
        $color = Color::where('label', 'Gray')->firstOrFail();
        $caliber = Caliber::where('label', '9mm')->firstOrFail();
        $purpose = Purpose::where('label', 'Range/Training')->firstOrFail();
        $cabinet = Location::factory()->create([...$owner, 'label' => 'Inventory cabinet']);
        $shelf = Location::factory()->childOf($cabinet)->create(['label' => 'Upper shelf']);
        $store = Store::factory()->create([...$owner, 'label' => 'Sample Supply']);
        $range = Range::create([...$owner, 'label' => 'Demo range', 'address' => '10 Example Lane', 'description' => 'Local sample facility']);
        $firearm = Firearm::factory()->create([...$owner, 'label' => 'Sample A', 'type' => 'handgun', 'location_id' => $shelf->id, 'color_id' => $color->id]);
        $firearm->calibers()->attach($caliber);
        Firearm::factory()->create([...$owner, 'label' => 'Retired sample', ...$archived]);
        Firearm::factory()->create([...$owner, 'label' => 'Unassigned sample', 'type' => 'rifle']);

        $ammoFields = [...$owner, 'caliber_id' => $caliber->id, 'purpose_id' => $purpose->id];
        $ammo = Ammunition::factory()->create([...$ammoFields, 'label' => 'Standard sample', 'reorder_min' => 50, 'reorder_target' => 200]);
        $ammo->update([
            'ammunition_casing_id' => DB::table('reference.ammunition_casings')->min('id'),
            'ammunition_condition_id' => DB::table('reference.ammunition_conditions')->min('id'),
            'bullet_type_id' => DB::table('reference.bullet_types')->min('id'),
            'primer_type_id' => DB::table('reference.primer_types')->min('id'),
        ]);
        $low = Ammunition::factory()->create([...$ammoFields, 'label' => 'Low stock sample', 'reorder_min' => 25, 'reorder_target' => 100]);
        $shotgun = Caliber::where('label', '12G')->firstOrFail();
        Ammunition::factory()->create([...$ammoFields, 'label' => 'Out of stock sample', 'caliber_id' => $shotgun->id,
            'weight' => null, 'shot_material_id' => DB::table('reference.shot_materials')->min('id'),
            'shell_length_id' => DB::table('reference.shell_lengths')->min('id'), 'shell_type_id' => DB::table('reference.shell_types')->min('id'),
            'shot_weight_id' => DB::table('reference.shot_weights')->min('id')]);

        $assets = ['firearm' => $firearm];
        foreach (array_diff_key(OrderAssetType::models(), ['firearm' => true, 'magazine' => true]) as $type => $class) {
            $mounted = $class::factory()->create([...$owner, 'firearm_id' => $firearm->id, 'color_id' => $color->id]);
            $class::factory()->create([...$owner, 'location_id' => $shelf->id]);
            $class::factory()->create([...$owner, ...$archived]);
            $assets[$type] = $mounted;
        }

        $assets['suppressor']->update(['caliber_id' => $caliber->id, 'nfa_approved_date' => today()->subMonths(6)]);
        $magazines = Magazine::factory()->count(28)->create([...$owner, 'manufacturer' => 'Example Works', 'model_name' => 'Series A',
            'model_number' => 'A-01', 'capacity' => 10, 'location_id' => $shelf->id]);
        foreach ($magazines as $index => $magazine) {
            $magazine->calibers()->attach($caliber);
            $magazine->compatibleFirearms()->attach($firearm);
            $magazine->update(['id_marking' => 'A-'.($index + 1)]);
        }
        $magazines[0]->update(['loaded_rounds' => 10, 'loaded_ammunition_id' => $ammo->id, 'current_firearm_id' => $firearm->id, 'location_id' => null]);
        $magazines[1]->update(['loaded_rounds' => 5, 'loaded_ammunition_id' => $ammo->id]);
        $magazines->last()->update($archived);
        $assets['magazine'] = $magazines[0];

        $items = [
            ['ammunition_id' => $ammo->id, 'rounds' => 500, 'cost' => 100],
            ['ammunition_id' => $low->id, 'rounds' => 10, 'cost' => 2],
        ];
        foreach ($assets as $type => $asset) {
            $items[] = ['type' => $type, 'asset_id' => $asset->id, 'cost' => 25];
        }
        $order = resolve(SyncOrder::class)->execute(null, [
            'store_id' => $store->id, 'order_ref' => 'DEMO-001', 'order_date' => today()->subMonths(5)->toDateString(), 'items' => $items,
        ], $user->id);

        $picture = $this->createPicture($user, 'Overview', public_path('images/social-preview.png'));
        $detail = $this->createPicture($user, 'Detail', resource_path('front-end/src/assets/logo.png'));
        $targetPicture = $this->createPicture($user, 'Target', public_path('images/social-preview.png'));
        $this->createPicture($user, 'Unattached', resource_path('front-end/src/assets/logo.png'));
        foreach ([$ammo, $shelf, $store, $range, ...array_values($assets)] as $record) {
            $record->pictures()->attach($picture, [...$owner, 'is_primary' => true, 'sort_order' => 0]);
            $record->notes()->create([...$owner, 'note' => 'Reviewed for the sample inventory.', 'created_at' => now()->subDays(3), 'updated_at' => now()->subDay()]);
        }
        $firearm->pictures()->attach($detail, [...$owner, 'is_primary' => false, 'sort_order' => 1]);
        $order->notes()->create([...$owner, 'note' => 'Sample receipt verified.']);
        resolve(LogFirearmActivityEvent::class)->execute($firearm, $user->id, ['event_type' => 'CLEAN', 'event_date' => today()->toDateString()]);

        for ($index = 0; $index < 16; $index++) {
            $date = today()->subWeeks($index)->toDateString();
            $session = TrainingSession::factory()->create([...$owner, 'label' => 'Range visit '.(16 - $index), 'range_id' => $range->id,
                'location_id' => $shelf->id, 'session_date' => $date]);
            $line = $session->lines()->create([...$owner, 'firearm_id' => $firearm->id, 'ammunition_id' => $ammo->id,
                'suppressor_id' => $assets['suppressor']->id, 'rounds' => 20]);
            $line->inventoryDeduction()->create([...$owner, 'ammunition_id' => $ammo->id, 'rounds' => -20, 'inventory_date' => $date]);
            if ($index === 0) {
                $session->targets()->create([...$owner, 'label' => 'Sample target', 'distance' => 10, 'group_size' => 2,
                    'picture_id' => $targetPicture->id, 'firearm_id' => $firearm->id, 'bullet_id' => $ammo->id]);
                $session->notes()->create([...$owner, 'note' => 'First visit with a photo.']);
                $line->notes()->create([...$owner, 'note' => 'Sample line annotation.']);
            }
        }
        $ammo->recalculateInventory();
    }

    private function createPicture(User $user, string $name, string $filename): Picture
    {
        $picture = Picture::factory()->create([
            'user_id' => $user->id,
            'disk' => 'demo-pictures',
            'name' => $name.'.png',
            'key_prefix' => 'samples/'.$user->auth_uuid.'/'.strtolower($name),
            'processing_status' => PictureProcessingStatus::Pending,
            'mime_type' => 'image/png',
            'byte_size' => filesize($filename),
            'processed_at' => null,
        ]);
        Storage::disk($picture->disk)->put($picture->stagingKey(), file_get_contents($filename));
        (new ProcessPictureDerivatives($picture->id, $picture->processing_version))->handle();

        return $picture->refresh();
    }
}
