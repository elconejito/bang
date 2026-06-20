<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePictureRequest;
use App\Models\Picture;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

abstract class PictureControllerBase extends Controller
{
    protected function entityPictures(Model $entity): JsonResponse
    {
        $pictures = $entity->pictures()->get();

        return response()->json([
            'data' => $pictures->map(fn (Picture $p) => $this->transformPicture($p, get_class($entity), $entity->id))->values(),
        ]);
    }

    protected function storeEntityPicture(StorePictureRequest $request, Model $entity): JsonResponse
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

        $nextOrder = $entity->pictures()->count();
        $isFirst = $nextOrder === 0;

        $entity->pictures()->attach($picture->id, [
            'user_id' => Auth::id(),
            'sort_order' => $nextOrder,
            'is_primary' => $isFirst,
        ]);

        return response()->json(['data' => $this->transformPicture($picture->fresh(), get_class($entity), $entity->id)], 201);
    }

    protected function attachEntityPicture(Model $entity, Picture $picture): JsonResponse
    {
        if ($entity->pictures()->where('picture_id', $picture->id)->exists()) {
            return response()->json(['message' => 'Already attached.'], 409);
        }

        $nextOrder = $entity->pictures()->count();
        $isFirst = $nextOrder === 0;

        $entity->pictures()->attach($picture->id, [
            'user_id' => Auth::id(),
            'sort_order' => $nextOrder,
            'is_primary' => $isFirst,
        ]);

        return response()->json(['data' => $this->transformPicture($picture, get_class($entity), $entity->id)]);
    }

    protected function detachEntityPicture(Model $entity, Picture $picture): JsonResponse
    {
        $wasPrimary = $entity->pictures()
            ->where('picture_id', $picture->id)
            ->first()?->pivot?->is_primary;

        $entity->pictures()->detach($picture->id);

        if ($wasPrimary) {
            $first = $entity->pictures()->first();
            if ($first) {
                $entity->pictures()->updateExistingPivot($first->id, ['is_primary' => true]);
            }
        }

        return response()->json([], 204);
    }

    protected function setEntityPrimaryPicture(Model $entity, Picture $picture): JsonResponse
    {
        DB::table('cms.pictureables')
            ->where('pictureable_type', get_class($entity))
            ->where('pictureable_id', $entity->id)
            ->update(['is_primary' => false]);

        $entity->pictures()->updateExistingPivot($picture->id, ['is_primary' => true]);

        return response()->json([], 204);
    }

    protected function reorderEntityPictures(Request $request, Model $entity): JsonResponse
    {
        $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer']]);

        foreach ($request->input('ids') as $index => $pictureId) {
            $entity->pictures()->updateExistingPivot($pictureId, ['sort_order' => $index]);
        }

        return response()->json([], 204);
    }

    /**
     * @return array{id: int, name: string, filename: string, url: string, url_medium: string, url_large: string, is_primary: bool, sort_order: int, also_on_count: int, created_at: string}
     */
    protected function transformPicture(Picture $picture, string $modelClass, int $entityId): array
    {
        $fresh = Picture::with([])->find($picture->id);

        $pivot = DB::table('cms.pictureables')
            ->where('picture_id', $picture->id)
            ->where('pictureable_type', $modelClass)
            ->where('pictureable_id', $entityId)
            ->first();

        $alsoOnCount = DB::table('cms.pictureables')
            ->where('picture_id', $picture->id)
            ->where(fn ($q) => $q->where('pictureable_type', '!=', $modelClass)
                ->orWhere('pictureable_id', '!=', $entityId))
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
