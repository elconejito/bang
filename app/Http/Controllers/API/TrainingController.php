<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrainingSessionRequest;
use App\Models\Inventory;
use App\Models\TrainingSession;
use App\Transformers\TrainingSessionTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class TrainingController extends Controller
{
    public function index(): JsonResponse
    {
        $training = QueryBuilder::for(TrainingSession::class)
            ->allowedFilters(['label', 'session_date', 'location_id'])
            ->allowedSorts(['label', 'session_date'])
            ->defaultSort('-session_date')
            ->get();

        return fractal($training, TrainingSessionTransformer::class)->respond();
    }

    public function store(StoreTrainingSessionRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $trainingSession = TrainingSession::create([
                ...$request->only(['label', 'description', 'session_date', 'location_id']),
                'user_id' => Auth::id(),
            ]);

            foreach ($request->input('inventories', []) as $inventory) {
                Inventory::create([
                    ...$inventory,
                    'user_id' => Auth::id(),
                    'training_session_id' => $trainingSession->id,
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'error' => $e->getMessage()], 500);
        }

        return fractal()->item($trainingSession, TrainingSessionTransformer::class)->respond();
    }

    public function show(int $id): JsonResponse
    {
        return fractal()->item(TrainingSession::findOrFail($id), TrainingSessionTransformer::class)->respond();
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $session = TrainingSession::findOrFail($id);
        $session->update($request->only(['label', 'description', 'session_date', 'location_id']));

        return fractal()->item($session, TrainingSessionTransformer::class)->respond();
    }

    public function destroy(int $id): JsonResponse
    {
        TrainingSession::findOrFail($id)->delete();

        return response()->json(null, 204);
    }
}
