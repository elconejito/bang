<?php

namespace App\Actions\Firearms;

use App\Models\Light;
use App\Models\MiscAccessory;
use App\Models\Optic;
use App\Models\Suppressor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GetMountableAccessories
{
    /**
     * @return Collection<int, array{type: string, id: int, label: string, subtitle: string}>
     */
    public function execute(int $userId): Collection
    {
        return collect([
            ...$this->mountable(Suppressor::class, 'Suppressor', $userId),
            ...$this->mountable(Optic::class, 'Optic', $userId),
            ...$this->mountable(Light::class, 'Light', $userId),
            ...$this->mountable(MiscAccessory::class, 'Misc', $userId, true),
        ])->sortBy(['type', 'label'])->values();
    }

    /**
     * @param  class-string<Suppressor|Optic|Light|MiscAccessory>  $class
     * @return array<int, array{type: string, id: int, label: string, subtitle: string}>
     */
    private function mountable(string $class, string $type, int $userId, bool $excludeFits = false): array
    {
        return $class::query()
            ->where('user_id', $userId)
            ->whereNull('archived_at')
            ->whereNull('firearm_id')
            ->when($excludeFits, fn ($query) => $query->where(function ($query): void {
                $query->whereNull('sub_type')
                    ->orWhereNotIn(DB::raw('lower(sub_type)'), ['holster', 'case', 'bag']);
            }))
            ->orderBy('manufacturer')
            ->orderBy('label')
            ->get()
            ->map(fn ($accessory) => [
                'type' => $type,
                'id' => $accessory->id,
                'label' => $accessory->label,
                'subtitle' => collect([$accessory->manufacturer, $type === 'Misc' ? $accessory->sub_type : $type])
                    ->filter()
                    ->implode(' · '),
            ])
            ->all();
    }
}
