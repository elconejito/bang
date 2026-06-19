<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePictureRequest;
use App\Models\Firearm;
use App\Models\Picture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FirearmPictureController extends Controller
{
    /**
     * List pictures attached to a firearm, ordered by sort_order.
     */
    public function index(Firearm $firearm): JsonResponse
    {
        $this->authorize('view', $firearm);

        $pictures = $firearm->pictures()->get();

        return response()->json([
            'data' => $pictures->map(fn (Picture $p) => $this->transform($p, $firearm->id))->values(),
        ]);
    }

    /**
     * Upload a new picture and attach it to the firearm.
     */
    public function store(StorePictureRequest $request, Firearm $firearm): JsonResponse
    {
        $this->authorize('update', $firearm);

        $file = $request->file('image');
        $filename = Str::uuid().'.'.$file->getClientOriginalExtension();

        $file->storeAs('public/images', $filename);

        $picture = Picture::create([
            'name' => $request->input('name') ?: $file->getClientOriginalName(),
            'filename' => $filename,
            'user_id' => Auth::id(),
        ]);

        $picture->resize();

        $nextOrder = $firearm->pictures()->count();
        $isFirst = $nextOrder === 0;

        $firearm->pictures()->attach($picture->id, [
            'user_id' => Auth::id(),
            'sort_order' => $nextOrder,
            'is_primary' => $isFirst,
        ]);

        $picture->load([]);
        $picture->setRelation('pivot', $firearm->pictures()->where('picture_id', $picture->id)->first()?->pivot);

        return response()->json(['data' => $this->transform($picture->fresh(), $firearm->id)], 201);
    }

    /**
     * Attach an existing library picture to this firearm.
     */
    public function attach(Request $request, Firearm $firearm, Picture $picture): JsonResponse
    {
        $this->authorize('update', $firearm);

        if ($firearm->pictures()->where('picture_id', $picture->id)->exists()) {
            return response()->json(['message' => 'Already attached.'], 409);
        }

        $nextOrder = $firearm->pictures()->count();
        $isFirst = $nextOrder === 0;

        $firearm->pictures()->attach($picture->id, [
            'user_id' => Auth::id(),
            'sort_order' => $nextOrder,
            'is_primary' => $isFirst,
        ]);

        return response()->json(['data' => $this->transform($picture, $firearm->id)]);
    }

    /**
     * Detach a picture from the firearm (does not delete from library).
     */
    public function detach(Firearm $firearm, Picture $picture): JsonResponse
    {
        $this->authorize('update', $firearm);

        $wasPrimary = $firearm->pictures()
            ->where('picture_id', $picture->id)
            ->first()?->pivot?->is_primary;

        $firearm->pictures()->detach($picture->id);

        // If we removed the primary, promote the first remaining photo
        if ($wasPrimary) {
            $first = $firearm->pictures()->first();
            if ($first) {
                $firearm->pictures()->updateExistingPivot($first->id, ['is_primary' => true]);
            }
        }

        return response()->json([], 204);
    }

    /**
     * Set a picture as the primary for this firearm.
     */
    public function setPrimary(Firearm $firearm, Picture $picture): JsonResponse
    {
        $this->authorize('update', $firearm);

        // Clear all primaries for this firearm then set the chosen one
        DB::table('cms.pictureables')
            ->where('pictureable_type', Firearm::class)
            ->where('pictureable_id', $firearm->id)
            ->update(['is_primary' => false]);

        $firearm->pictures()->updateExistingPivot($picture->id, ['is_primary' => true]);

        return response()->json([], 204);
    }

    /**
     * Reorder pictures for this firearm.
     *
     * @param  Request  $request  body: { ids: int[] } in desired order
     */
    public function reorder(Request $request, Firearm $firearm): JsonResponse
    {
        $this->authorize('update', $firearm);

        $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer']]);

        foreach ($request->input('ids') as $index => $pictureId) {
            $firearm->pictures()->updateExistingPivot($pictureId, ['sort_order' => $index]);
        }

        return response()->json([], 204);
    }

    /**
     * @return array{id: int, name: string, filename: string, url: string, url_medium: string, url_large: string, is_primary: bool, sort_order: int, also_on_count: int, created_at: string}
     */
    private function transform(Picture $picture, int $firearmId): array
    {
        $fresh = Picture::with([])->find($picture->id);

        $pivot = DB::table('cms.pictureables')
            ->where('picture_id', $picture->id)
            ->where('pictureable_type', Firearm::class)
            ->where('pictureable_id', $firearmId)
            ->first();

        $alsoOnCount = DB::table('cms.pictureables')
            ->where('picture_id', $picture->id)
            ->where(fn ($q) => $q->where('pictureable_type', '!=', Firearm::class)
                ->orWhere('pictureable_id', '!=', $firearmId))
            ->count();

        return [
            'id' => $fresh->id,
            'name' => $fresh->name,
            'filename' => $fresh->filename,
            'url' => $fresh->getUrl('thumbnail'),
            'url_medium' => $fresh->getUrl('medium'),
            'url_large' => $fresh->getUrl('large'),
            'is_primary' => (bool) ($pivot?->is_primary ?? false),
            'sort_order' => (int) ($pivot?->sort_order ?? 0),
            'also_on_count' => $alsoOnCount,
            'created_at' => $fresh->created_at->toISOString(),
        ];
    }
}
