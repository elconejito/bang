<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AccessoryEvent;
use App\Models\Ammunition;
use App\Models\Firearm;
use App\Models\Inventory;
use App\Models\SessionLine;
use App\Models\Suppressor;
use App\Models\TrainingSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Return all data needed for the main dashboard in a single request.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Firearm::class);

        $twelveMonthsAgo = now()->subMonths(12);
        $userId = Auth::id();

        // ── Stats strip ──────────────────────────────────────────────
        $firearmsCount = Firearm::count();
        $roundsOnHand = (int) Ammunition::sum('inventory');

        $roundsFired12mo = (int) SessionLine::whereHas('trainingSession', fn ($q) => $q->where('session_date', '>=', $twelveMonthsAgo)
        )->where('add_firearm_count', true)->sum('rounds');

        $sessions12mo = TrainingSession::where('session_date', '>=', $twelveMonthsAgo)->count();

        $ammoCost12mo = (float) Inventory::where('inventory_date', '>=', $twelveMonthsAgo)
            ->where('cost', '>', 0)
            ->sum('cost');

        $lastSessionDate = TrainingSession::orderByDesc('session_date')->value('session_date');
        $daysSinceLastSession = $lastSessionDate
            ? (int) now()->startOfDay()->diffInDays($lastSessionDate->startOfDay())
            : null;

        // ── Ammo on hand by caliber ──────────────────────────────────
        $allAmmo = Ammunition::with('caliber')->get();

        $ammoByCaliberRaw = $allAmmo
            ->groupBy('caliber_id')
            ->map(function ($ammos) {
                $caliber = $ammos->first()->caliber;
                $onHand = (int) $ammos->sum(fn ($a) => (int) ($a->getAttributes()['inventory'] ?? 0));
                $withMin = $ammos->filter(fn ($a) => $a->reorder_min !== null);
                $minReorder = $withMin->min('reorder_min');
                $isLow = $minReorder !== null && $onHand < $minReorder;

                return [
                    'caliber_id' => $caliber?->id,
                    'caliber_label' => $caliber?->label ?? 'Unknown',
                    'on_hand' => $onHand,
                    'is_low' => $isLow,
                ];
            })
            ->filter(fn ($c) => $c['on_hand'] > 0)
            ->sortByDesc('on_hand')
            ->values();

        $maxOnHand = $ammoByCaliberRaw->max('on_hand') ?: 1;
        $ammoByCalibter = $ammoByCaliberRaw->map(fn ($c) => [
            ...$c,
            'bar_pct' => (int) round(($c['on_hand'] / $maxOnHand) * 100),
        ])->values();

        // ── Low stock ────────────────────────────────────────────────
        $lowStockAmmo = Ammunition::with('caliber')
            ->whereNotNull('reorder_min')
            ->whereRaw('inventory < reorder_min')
            ->orderBy('inventory')
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'label' => $a->label,
                'caliber' => $a->caliber?->label,
                'on_hand' => (int) ($a->getAttributes()['inventory'] ?? 0),
                'reorder_min' => $a->reorder_min,
            ])
            ->values();

        // ── Pending NFA ──────────────────────────────────────────────
        $pendingNfa = Suppressor::whereNull('nfa_approved_date')
            ->where('is_nfa', true)
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'label' => $s->label,
                'form_type' => $s->nfa_form_type,
                'submitted_at' => $s->purchase_date?->toDateString(),
            ])
            ->values();

        // ── Most shot firearms (12 mo) ────────────────────────────────
        $topRounds = DB::table('cms.session_lines as sl')
            ->join('cms.training_sessions as ts', 'ts.id', '=', 'sl.training_session_id')
            ->where('sl.user_id', $userId)
            ->where('ts.user_id', $userId)
            ->where('sl.add_firearm_count', true)
            ->where('ts.session_date', '>=', $twelveMonthsAgo)
            ->groupBy('sl.firearm_id')
            ->orderByDesc('rounds_12mo')
            ->limit(3)
            ->select('sl.firearm_id', DB::raw('SUM(sl.rounds) as rounds_12mo'))
            ->get()
            ->keyBy('firearm_id');

        $mostShot = Firearm::with('pictures')
            ->whereIn('id', $topRounds->keys())
            ->get()
            ->map(function (Firearm $firearm) use ($topRounds) {
                $primaryPicture = $firearm->pictures->first(fn ($p) => $p->pivot->is_primary)
                    ?? $firearm->pictures->first();

                return [
                    'id' => $firearm->id,
                    'label' => $firearm->label ?? $firearm->manufacturer,
                    'manufacturer' => $firearm->manufacturer,
                    'model' => $firearm->model,
                    'rounds_12mo' => (int) ($topRounds[$firearm->id]->rounds_12mo ?? 0),
                    'primary_photo_url' => $primaryPicture?->getUrl('thumbnail'),
                ];
            })
            ->sortByDesc('rounds_12mo')
            ->values();

        // ── Recent activity ──────────────────────────────────────────
        $rangeActivity = TrainingSession::with('lines')
            ->orderByDesc('session_date')
            ->take(10)
            ->get()
            ->map(function (TrainingSession $session) {
                $rounds = (int) $session->lines->sum('rounds');
                $gunCount = $session->lines->pluck('firearm_id')->unique()->filter()->count();

                return [
                    'type' => 'RANGE',
                    'date' => $session->session_date->toDateString(),
                    'label' => $session->label,
                    'subtitle' => "{$rounds} rds".($gunCount > 1 ? ", {$gunCount} guns" : ''),
                    'id' => $session->id,
                ];
            });

        $stockActivity = Inventory::with(['bullet.caliber'])
            ->where('rounds', '>', 0)
            ->whereNotNull('order_id')
            ->orderByDesc('inventory_date')
            ->take(10)
            ->get()
            ->map(fn (Inventory $inv) => [
                'type' => 'STOCK',
                'date' => $inv->inventory_date->toDateString(),
                'label' => "+{$inv->rounds} {$inv->bullet?->label}",
                'subtitle' => $inv->bullet?->caliber?->label,
                'id' => $inv->id,
            ]);

        $mountActivity = AccessoryEvent::with('accessoryable')
            ->orderByDesc('event_date')
            ->take(10)
            ->get()
            ->map(fn (AccessoryEvent $event) => [
                'type' => str_contains(strtolower((string) $event->event_type), 'unmount') ? 'UNMOUNT' : 'MOUNT',
                'date' => $event->event_date->toDateString(),
                'label' => (str_contains(strtolower((string) $event->event_type), 'unmount') ? 'Unmounted ' : 'Mounted ')
                    .($event->accessoryable?->label ?? 'accessory'),
                'subtitle' => $event->description,
                'id' => $event->id,
            ]);

        $recentActivity = $rangeActivity
            ->concat($stockActivity)
            ->concat($mountActivity)
            ->sortByDesc('date')
            ->take(5)
            ->values();

        return response()->json([
            'data' => [
                'stats' => [
                    'firearms_count' => $firearmsCount,
                    'rounds_on_hand' => $roundsOnHand,
                    'rounds_fired_12mo' => $roundsFired12mo,
                    'sessions_12mo' => $sessions12mo,
                    'ammo_cost_12mo' => $ammoCost12mo,
                    'days_since_last_session' => $daysSinceLastSession,
                ],
                'ammo_by_caliber' => $ammoByCalibter,
                'low_stock_ammo' => $lowStockAmmo,
                'pending_nfa' => $pendingNfa,
                'most_shot_firearms' => $mostShot,
                'recent_activity' => $recentActivity,
            ],
        ]);
    }
}
