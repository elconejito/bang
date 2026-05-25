<?php

namespace App\Http\Controllers\API\Ammunition;

use App\Http\Controllers\Controller;
use App\Models\Ammunition;
use App\Transformers\NoteTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index(Ammunition $ammunition): JsonResponse
    {
        $this->authorize('view', $ammunition);

        // TODO: implement paginated notes — see Notable/photos phase in TODO.md
        return response()->json(['data' => []], 200);
    }

    public function store(Ammunition $ammunition, Request $request): JsonResponse
    {
        $this->authorize('view', $ammunition);

        $note = $ammunition->notes()->create([
            'user_id' => auth()->id(),
            'note' => $request->get('note'),
        ]);

        return fractal()->item($note, NoteTransformer::class)->respond();
    }
}
