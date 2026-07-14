<?php

namespace App\Http\Controllers\API;

use App\Enums\NotableType;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexNotesRequest;
use App\Http\Requests\StoreNoteRequest;
use App\Models\Note;
use App\Transformers\NoteTransformer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class NoteController extends Controller
{
    public function index(IndexNotesRequest $request, NotableType $notableType, int $notable): JsonResponse
    {
        $notableModel = $this->resolveNotable($notableType, $notable);
        $this->authorize('view', $notableModel);
        $validated = $request->validated();
        $search = $validated['search'] ?? null;
        $notes = $notableModel->notes()
            ->select(['id', 'user_id', 'note', 'notable_id', 'notable_type', 'created_at', 'updated_at'])
            ->when($search, fn (Builder $query, string $search): Builder => $query->whereLike('note', "%{$search}%", caseSensitive: false))
            ->latest()
            ->latest('id')
            ->paginate($validated['per_page'] ?? 10);
        $transformer = new NoteTransformer;

        return response()->json([
            'data' => $notes->getCollection()->map(fn (Note $note): array => $transformer->transform($note))->all(),
            'meta' => [
                'current_page' => $notes->currentPage(),
                'last_page' => $notes->lastPage(),
                'per_page' => $notes->perPage(),
                'from' => $notes->firstItem(),
                'to' => $notes->lastItem(),
                'total' => $notes->total(),
            ],
            'links' => [
                'first' => $notes->url(1),
                'last' => $notes->url($notes->lastPage()),
                'prev' => $notes->previousPageUrl(),
                'next' => $notes->nextPageUrl(),
            ],
        ]);
    }

    public function store(StoreNoteRequest $request, NotableType $notableType, int $notable): JsonResponse
    {
        $notableModel = $this->resolveNotable($notableType, $notable);
        $this->authorize('update', $notableModel);
        $validated = $request->validated();
        $note = $notableModel->notes()->create([
            'user_id' => $request->user()->id,
            'note' => $validated['note'],
        ]);

        return response()->json([
            'data' => (new NoteTransformer)->transform($note),
        ], 201);
    }

    private function resolveNotable(NotableType $notableType, int $notable): Model
    {
        $modelClass = $notableType->modelClass();

        return $modelClass::query()->findOrFail($notable);
    }
}
