<?php

namespace App\Http\Controllers\API\Firearms;

use App\Http\Controllers\Controller;
use App\Models\Firearm;
use App\Transformers\NoteTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index(Firearm $firearm): JsonResponse
    {
        $this->authorize('view', $firearm);

        // TODO: implement paginated notes — see Notable/photos phase in TODO.md
        return response()->json(['data' => []], 200);
    }

    public function store(Firearm $firearm, Request $request): JsonResponse
    {
        $this->authorize('view', $firearm);

        $note = $firearm->notes()->create([
            'user_id' => auth()->id(),
            'note' => $request->get('note'),
        ]);

        return fractal()->item($note, NoteTransformer::class)->respond();
    }
}
