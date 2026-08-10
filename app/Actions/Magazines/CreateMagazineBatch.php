<?php

namespace App\Actions\Magazines;

use App\Models\Magazine;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class CreateMagazineBatch
{
    /**
     * @param  array<string, mixed>  $attributes
     * @return Collection<int, Magazine>
     */
    public function handle(User $user, array $attributes): Collection
    {
        return DB::transaction(function () use ($user, $attributes): Collection {
            $magazines = new Collection;
            $quantity = (int) $attributes['quantity'];
            $prefix = $attributes['marking_prefix'] ?? null;
            $start = (int) ($attributes['marking_start'] ?? 1);
            $width = (int) ($attributes['marking_width'] ?? 3);
            $calibers = $attributes['calibers'] ?? [];
            $firearms = $attributes['firearms'] ?? [];

            for ($offset = 0; $offset < $quantity; $offset++) {
                $magazine = Magazine::query()->create([
                    'manufacturer' => $attributes['manufacturer'],
                    'label' => $attributes['label'] ?? null,
                    'model_name' => $attributes['model_name'] ?? null,
                    'model_number' => $attributes['model_number'] ?? null,
                    'capacity' => $attributes['capacity'],
                    'color_id' => $attributes['color_id'] ?? null,
                    'id_marking' => $prefix === null ? null : $prefix.str_pad((string) ($start + $offset), $width, '0', STR_PAD_LEFT),
                    'location_id' => $attributes['location_id'] ?? null,
                    'loaded_rounds' => 0,
                    'loaded_ammunition_id' => null,
                    'current_firearm_id' => null,
                    'user_id' => $user->getKey(),
                ]);
                $magazine->calibers()->sync($calibers);
                $magazine->compatibleFirearms()->sync($firearms);
                $magazines->push($magazine);
            }

            return $magazines->load(['calibers', 'compatibleFirearms', 'loadedAmmunition', 'location', 'currentFirearm', 'pictures']);
        });
    }
}
