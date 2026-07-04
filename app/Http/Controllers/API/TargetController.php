<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Picture;
use App\Models\Target;
use App\Models\TrainingSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TargetController extends Controller
{
    /**
     * @param  Request  $request
     * @param  TrainingSession  $training
     * @return JsonResponse
     */
    public function store(Request $request, TrainingSession $training): JsonResponse
    {
        $this->authorize('update', $training);

        $request->validate([
            'image' => 'required|image|max:10240',
            'label' => 'nullable|string|max:255',
            'distance' => 'required|numeric|min:0',
            'group_size' => 'required|numeric|min:0',
            'firearm_id' => 'nullable|integer|exists:firearms,id',
            'bullet_id' => 'nullable|integer|exists:ammunition,id',
        ]);

        $file = $request->file('image');
        $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
        $file->storeAs('public/images', $filename);

        $picture = Picture::create([
            'name' => $request->input('label') ?: $file->getClientOriginalName(),
            'filename' => $filename,
            'user_id' => Auth::id(),
        ]);
        $picture->resize();

        $target = $training->targets()->create([
            'label' => $request->input('label'),
            'distance' => $request->input('distance'),
            'group_size' => $request->input('group_size'),
            'picture_id' => $picture->id,
            'firearm_id' => $request->input('firearm_id'),
            'bullet_id' => $request->input('bullet_id'),
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'data' => [
                'id' => $target->id,
                'label' => $target->label,
                'distance' => (float) $target->distance,
                'group_size' => (float) $target->group_size,
                'thumbnail_url' => $picture->getUrl('thumbnail'),
                'medium_url' => $picture->getUrl('medium'),
            ],
        ], 201);
    }

    /**
     * @param  TrainingSession  $training
     * @param  Target  $target
     * @return JsonResponse
     */
    public function destroy(TrainingSession $training, Target $target): JsonResponse
    {
        $this->authorize('delete', $target);

        $target->delete();

        return response()->json(null, 204);
    }
}
