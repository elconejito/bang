<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePictureRequest;
use App\Models\Picture;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PictureController extends Controller
{
    /**
     * All pictures in the user's library (for the picker modal).
     */
    public function index(): JsonResponse
    {
        $pictures = Picture::orderByDesc('created_at')->get();

        return response()->json([
            'data' => $pictures->map(fn (Picture $p) => $this->transform($p))->values(),
        ]);
    }

    /**
     * Upload a new picture to the library.
     */
    public function store(StorePictureRequest $request): JsonResponse
    {
        $file = $request->file('image');
        $filename = Str::uuid().'.'.$file->getClientOriginalExtension();

        $file->storeAs('public/images', $filename);

        $picture = Picture::create([
            'name' => $request->input('name') ?: $file->getClientOriginalName(),
            'filename' => $filename,
            'user_id' => Auth::id(),
        ]);

        $picture->resize();

        return response()->json(['data' => $this->transform($picture)], 201);
    }

    /**
     * @param  Picture  $picture
     * @return array{id: int, name: string, filename: string, url: string, url_medium: string, url_large: string, created_at: string}
     */
    public function transform(Picture $picture): array
    {
        return [
            'id' => $picture->id,
            'name' => $picture->name,
            'filename' => $picture->filename,
            'url' => $picture->getUrl('thumbnail'),
            'url_medium' => $picture->getUrl('medium'),
            'url_large' => $picture->getUrl('large'),
            'created_at' => $picture->created_at->toISOString(),
        ];
    }
}
