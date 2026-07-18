<?php

namespace App\Actions\Ammunition;

use App\Models\Ammunition;
use Carbon\CarbonImmutable;

class BuildAmmunitionStats
{
    /**
     * @return array{months: array<int, array{key: string, label: string, on_hand: int, purchase_cost_per_round: float}>, average_purchase_cost_per_round: float|null, estimated_current_value: float|null, peak_on_hand: int, purchase_cost_range: array{min: float, max: float}|null}
     */
    public function handle(Ammunition $ammunition): array
    {
        $lastMonth = CarbonImmutable::now()->startOfMonth();
        $firstMonth = $lastMonth->subMonths(11);
        $afterLastMonth = $lastMonth->addMonth();

        $priorBalance = (int) $ammunition->inventories()
            ->where('inventory_date', '<', $firstMonth->toDateString())
            ->sum('rounds');

        $monthlyActivity = $ammunition->inventories()
            ->whereBetween('inventory_date', [$firstMonth->toDateString(), $afterLastMonth->subDay()->toDateString()])
            ->selectRaw("to_char(inventory_date, 'YYYY-MM') as month_key")
            ->selectRaw('SUM(rounds) as rounds_delta')
            ->selectRaw('SUM(CASE WHEN order_id IS NOT NULL AND rounds > 0 AND cost > 0 THEN cost ELSE 0 END) as purchase_cost')
            ->selectRaw('SUM(CASE WHEN order_id IS NOT NULL AND rounds > 0 AND cost > 0 THEN rounds ELSE 0 END) as purchase_rounds')
            ->groupByRaw("to_char(inventory_date, 'YYYY-MM')")
            ->get()
            ->keyBy('month_key');

        $purchaseTotals = $ammunition->inventories()
            ->whereNotNull('order_id')
            ->where('rounds', '>', 0)
            ->where('cost', '>', 0)
            ->selectRaw('SUM(cost) as purchase_cost, SUM(rounds) as purchase_rounds')
            ->first();

        $balance = $priorBalance;
        $months = [];

        for ($index = 0; $index < 12; $index++) {
            $month = $firstMonth->addMonths($index);
            $activity = $monthlyActivity->get($month->format('Y-m'));
            $balance += (int) ($activity?->rounds_delta ?? 0);
            $purchaseRounds = (int) ($activity?->purchase_rounds ?? 0);
            $purchaseCost = (float) ($activity?->purchase_cost ?? 0);

            $months[] = [
                'key' => $month->format('Y-m'),
                'label' => $month->format('M'),
                'on_hand' => max(0, $balance),
                'purchase_cost_per_round' => $purchaseRounds > 0 ? $purchaseCost / $purchaseRounds : 0.0,
            ];
        }

        $totalPurchaseRounds = (int) ($purchaseTotals?->purchase_rounds ?? 0);
        $averageCost = $totalPurchaseRounds > 0
            ? (float) $purchaseTotals->purchase_cost / $totalPurchaseRounds
            : null;
        $monthlyCosts = collect($months)->pluck('purchase_cost_per_round')->filter(fn (float $cost): bool => $cost > 0);

        return [
            'months' => $months,
            'average_purchase_cost_per_round' => $averageCost,
            'estimated_current_value' => $averageCost === null ? null : $averageCost * $ammunition->inventory,
            'peak_on_hand' => collect($months)->max('on_hand') ?? 0,
            'purchase_cost_range' => $monthlyCosts->isEmpty()
                ? null
                : ['min' => $monthlyCosts->min(), 'max' => $monthlyCosts->max()],
        ];
    }
}
