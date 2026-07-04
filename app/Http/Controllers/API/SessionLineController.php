<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSessionLineRequest;
use App\Http\Requests\UpdateSessionLineRequest;
use App\Models\Ammunition;
use App\Models\Inventory;
use App\Models\SessionLine;
use App\Models\TrainingSession;
use App\Transformers\SessionLineTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SessionLineController extends Controller
{
    /**
     * @param  StoreSessionLineRequest  $request
     * @param  TrainingSession  $training
     * @return JsonResponse
     */
    public function store(StoreSessionLineRequest $request, TrainingSession $training): JsonResponse
    {
        $this->authorize('update', $training);

        try {
            DB::beginTransaction();

            $line = $training->lines()->create([
                ...$request->safe()->except([]),
                'user_id' => Auth::id(),
            ]);

            if ($line->deduct_ammo) {
                $this->createInventoryDeduction($line, $training);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }

        if ($line->deduct_ammo) {
            Ammunition::withoutGlobalScopes()->find($line->ammunition_id)?->recalculateInventory();
        }

        $line->load(['firearm', 'ammunition', 'suppressor']);

        return fractal($line, SessionLineTransformer::class)->respond();
    }

    /**
     * @param  UpdateSessionLineRequest  $request
     * @param  TrainingSession  $training
     * @param  SessionLine  $sessionLine
     * @return JsonResponse
     */
    public function update(UpdateSessionLineRequest $request, TrainingSession $training, SessionLine $sessionLine): JsonResponse
    {
        $this->authorize('update', $sessionLine);

        try {
            DB::beginTransaction();

            $wasDeductingAmmo = $sessionLine->deduct_ammo;
            $sessionLine->update($request->safe()->except([]));
            $sessionLine->refresh();

            $deductionChanged = $request->has('deduct_ammo') || $request->has('rounds') || $request->has('ammunition_id');

            if ($deductionChanged) {
                if ($sessionLine->deduct_ammo && ! $wasDeductingAmmo) {
                    $this->createInventoryDeduction($sessionLine, $training);
                } elseif (! $sessionLine->deduct_ammo && $wasDeductingAmmo) {
                    $sessionLine->inventoryDeduction?->delete();
                } elseif ($sessionLine->deduct_ammo) {
                    $sessionLine->inventoryDeduction?->update([
                        'ammunition_id' => $sessionLine->ammunition_id,
                        'rounds' => -$sessionLine->rounds,
                    ]);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }

        if ($deductionChanged) {
            Ammunition::withoutGlobalScopes()->find($sessionLine->ammunition_id)?->recalculateInventory();
        }

        $sessionLine->load(['firearm', 'ammunition', 'suppressor']);

        return fractal($sessionLine, SessionLineTransformer::class)->respond();
    }

    /**
     * @param  TrainingSession  $training
     * @param  SessionLine  $sessionLine
     * @return JsonResponse
     */
    public function destroy(TrainingSession $training, SessionLine $sessionLine): JsonResponse
    {
        $this->authorize('delete', $sessionLine);

        $ammunitionId = $sessionLine->deduct_ammo ? $sessionLine->ammunition_id : null;

        try {
            DB::beginTransaction();
            $sessionLine->inventoryDeduction?->delete();
            $sessionLine->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }

        if ($ammunitionId) {
            Ammunition::withoutGlobalScopes()->find($ammunitionId)?->recalculateInventory();
        }

        return response()->json(null, 204);
    }

    private function createInventoryDeduction(SessionLine $line, TrainingSession $session): void
    {
        Inventory::create([
            'ammunition_id' => $line->ammunition_id,
            'rounds' => -$line->rounds,
            'inventory_date' => $session->session_date,
            'session_line_id' => $line->id,
            'user_id' => $line->user_id,
            'cost' => 0,
        ]);
    }
}
