<?php

namespace App\Http\Controllers\API\Ammunition;

use App\Http\Controllers\Controller;
use App\Models\Ammunition;
use App\Transformers\NoteTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index(int $ammunition_id): JsonResponse
    {
        // TODO: implement paginated notes — see Notable/photos phase in TODO.md
        return response()->json(['data' => []], 200);
    }

    public function store(int $ammunition_id, Request $request): JsonResponse
    {
        $ammunition = Ammunition::findOrFail($ammunition_id);

        $note = $ammunition->notes()->create([
            'user_id' => auth()->id(),
            'note' => $request->get('note'),
        ]);

        return fractal()->item($note, NoteTransformer::class)->respond();
    }
}
