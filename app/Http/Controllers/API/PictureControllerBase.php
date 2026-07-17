<?php

namespace App\Http\Controllers\API;

use App\Actions\Pictures\AttachPicture;
use App\Actions\Pictures\DetachPicture;
use App\Actions\Pictures\ReorderPictures;
use App\Actions\Pictures\SetPrimaryPicture;
use App\Actions\Pictures\UploadPicture;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePictureRequest;
use App\Models\Picture;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class PictureControllerBase extends Controller
{
    protected function entityPictures(Model $entity): JsonResponse
    {
        $pictures = $entity->pictures()->get();

        return response()->json(['data' => $pictures->map(fn (Picture $picture) => $this->transformPicture($picture))]);
    }

    protected function storeEntityPicture(StorePictureRequest $request, Model $entity): JsonResponse
    {
        $upload = resolve(UploadPicture::class);
        $attach = resolve(AttachPicture::class);
        $picture = $upload->execute($request->user(), $request->file('image'), $request->validated('name'));
        $attach->execute($entity, $picture);

        return response()->json(['data' => $this->transformPicture($entity->pictures()->findOrFail($picture->id))], 201);
    }

    protected function attachEntityPicture(Model $entity, Picture $picture): JsonResponse
    {
        $attach = resolve(AttachPicture::class);
        $attach->execute($entity, $picture);

        return response()->json(['data' => $this->transformPicture($entity->pictures()->findOrFail($picture->id))]);
    }

    protected function detachEntityPicture(Model $entity, Picture $picture): JsonResponse
    {
        $detach = resolve(DetachPicture::class);
        $detach->execute($entity, $picture);

        return response()->json(null, 204);
    }

    protected function setEntityPrimaryPicture(Model $entity, Picture $picture): JsonResponse
    {
        $setPrimary = resolve(SetPrimaryPicture::class);
        $setPrimary->execute($entity, $picture);

        return response()->json(null, 204);
    }

    protected function reorderEntityPictures(Request $request, Model $entity): JsonResponse
    {
        $validated = $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['required', 'integer', 'distinct']]);
        resolve(ReorderPictures::class)->execute($entity, $validated['ids']);

        return response()->json(null, 204);
    }

    protected function transformPicture(Picture $picture): array
    {
        return [
            'id' => $picture->id,
            'uuid' => $picture->uuid,
            'name' => $picture->name,
            'processing_status' => $picture->processing_status->value,
            'url' => $picture->getUrl('thumbnail'),
            'thumbnail_url' => $picture->getUrl('thumbnail'),
            'card_url' => $picture->getUrl('card'),
            'large_url' => $picture->getUrl('large'),
            'is_primary' => (bool) $picture->pivot->is_primary,
            'sort_order' => (int) $picture->pivot->sort_order,
            'created_at' => $picture->created_at->toISOString(),
        ];
    }
}
