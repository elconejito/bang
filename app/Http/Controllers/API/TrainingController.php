<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrainingSessionRequest;
use App\Http\Requests\UpdateTrainingSessionRequest;
use App\Models\Inventory;
use App\Models\SessionLine;
use App\Models\TrainingSession;
use App\Transformers\TrainingSessionTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class TrainingController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', TrainingSession::class);

        $sessions = QueryBuilder::for(TrainingSession::class)
            ->allowedFilters('label', 'session_date', 'range_id')
            ->allowedSorts('label', 'session_date')
            ->with(['range', 'lines.firearm', 'lines.ammunition', 'lines.suppressor', 'targets'])
            ->defaultSort('-session_date')
            ->get();

        return fractal($sessions, TrainingSessionTransformer::class)->respond();
    }

    /**
     * @param  StoreTrainingSessionRequest  $request
     * @return JsonResponse
     */
    public function store(StoreTrainingSessionRequest $request): JsonResponse
    {
        $this->authorize('create', TrainingSession::class);

        try {
            DB::beginTransaction();

            $session = TrainingSession::create([
                ...$request->safe()->except(['lines']),
                'user_id' => Auth::id(),
            ]);

            foreach ($request->safe()->input('lines', []) as $lineData) {
                $line = $session->lines()->create([
                    ...$lineData,
                    'user_id' => Auth::id(),
                ]);

                if ($line->deduct_ammo) {
                    Inventory::create([
                        'ammunition_id' => $line->ammunition_id,
                        'rounds' => -$line->rounds,
                        'inventory_date' => $session->session_date,
                        'session_line_id' => $line->id,
                        'user_id' => Auth::id(),
                        'cost' => 0,
                    ]);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }

        $session->load(['range', 'lines.firearm', 'lines.ammunition', 'lines.suppressor', 'targets']);

        return fractal($session, TrainingSessionTransformer::class)->respond();
    }

    /**
     * @param  TrainingSession  $training
     * @return JsonResponse
     */
    public function show(TrainingSession $training): JsonResponse
    {
        $this->authorize('view', $training);

        $training->load(['range', 'lines.firearm', 'lines.ammunition', 'lines.suppressor', 'targets']);

        return fractal($training, TrainingSessionTransformer::class)->respond();
    }

    /**
     * @param  UpdateTrainingSessionRequest  $request
     * @param  TrainingSession  $training
     * @return JsonResponse
     */
    public function update(UpdateTrainingSessionRequest $request, TrainingSession $training): JsonResponse
    {
        $this->authorize('update', $training);

        $training->update($request->safe()->except([]));
        $training->load(['range', 'lines.firearm', 'lines.ammunition', 'lines.suppressor', 'targets']);

        return fractal($training, TrainingSessionTransformer::class)->respond();
    }

    /**
     * @param  TrainingSession  $training
     * @return JsonResponse
     */
    public function destroy(TrainingSession $training): JsonResponse
    {
        $this->authorize('delete', $training);

        $training->delete();

        return response()->json(null, 204);
    }

    /**
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        $this->authorize('viewAny', TrainingSession::class);

        $year = now()->year;

        $sessionsThisYear = TrainingSession::whereYear('session_date', $year)->count();

        $roundsThisYear = SessionLine::whereHas('trainingSession', function ($q) use ($year) {
            $q->whereYear('session_date', $year);
        })->sum('rounds');

        $lastSession = TrainingSession::orderByDesc('session_date')->value('session_date');

        return response()->json([
            'data' => [
                'sessions_this_year' => $sessionsThisYear,
                'rounds_this_year' => (int) $roundsThisYear,
                'last_session_date' => $lastSession?->toDateString(),
            ],
        ]);
    }
}
