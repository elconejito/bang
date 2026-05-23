<?php

namespace App\Http\Controllers\API\Firearms;

use App\Http\Controllers\Controller;
use App\Models\Firearm;
use App\Transformers\NoteTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index(int $firearm_id): JsonResponse
    {
        // TODO: implement paginated notes — see Notable/photos phase in TODO.md
        return response()->json(['data' => []], 200);
    }

    public function store(int $firearm_id, Request $request): JsonResponse
    {
        $firearm = Firearm::findOrFail($firearm_id);

        $note = $firearm->notes()->create([
            'user_id' => auth()->id(),
            'note' => $request->get('note'),
        ]);

        return fractal()->item($note, NoteTransformer::class)->respond();
    }
}
