<?php

namespace App\Http\Controllers\API;

use App\Actions\Pictures\DeletePicture;
use App\Actions\Pictures\UploadPicture;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePictureRequest;
use App\Models\Picture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PictureController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Picture::class);
        $pictures = Picture::query()->orderByDesc('created_at')->paginate(48);
        $usage = DB::table('cms.pictureables')
            ->selectRaw('picture_id, count(*) as attachments_count, sum(case when is_primary then 1 else 0 end) as primary_usage_count')
            ->whereIn('picture_id', $pictures->getCollection()->pluck('id'))
            ->groupBy('picture_id')
            ->get()
            ->keyBy('picture_id');
        $targetUsage = DB::table('cms.targets')
            ->selectRaw('picture_id, count(*) as attachments_count')
            ->whereIn('picture_id', $pictures->getCollection()->pluck('id'))
            ->groupBy('picture_id')
            ->pluck('attachments_count', 'picture_id');

        return response()->json([
            'data' => $pictures->getCollection()->map(function (Picture $picture) use ($targetUsage, $usage): array {
                $pictureUsage = $usage->get($picture->id);

                $attachmentsCount = (int) ($pictureUsage?->attachments_count ?? 0) + (int) ($targetUsage->get($picture->id) ?? 0);

                return $this->transform($picture, $attachmentsCount, (int) ($pictureUsage?->primary_usage_count ?? 0));
            }),
            'meta' => ['current_page' => $pictures->currentPage(), 'last_page' => $pictures->lastPage(), 'total' => $pictures->total()],
        ]);
    }

    public function store(StorePictureRequest $request, UploadPicture $uploadPicture): JsonResponse
    {
        $this->authorize('create', Picture::class);
        $picture = $uploadPicture->execute($request->user(), $request->file('image'), $request->validated('name'));

        return response()->json(['data' => $this->transform($picture)], 201);
    }

    public function urls(Picture $picture): JsonResponse
    {
        $this->authorize('view', $picture);

        return response()->json(['data' => $this->transform($picture)]);
    }

    public function destroy(Picture $picture, DeletePicture $deletePicture): JsonResponse
    {
        $this->authorize('delete', $picture);
        $deletePicture->execute($picture);

        return response()->json(null, 204);
    }

    public function transform(Picture $picture, int $attachmentsCount = 0, int $primaryUsageCount = 0): array
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
            'attachments_count' => $attachmentsCount,
            'primary_usage_count' => $primaryUsageCount,
            'can_delete' => $attachmentsCount === 0,
            'deletion_reason' => $attachmentsCount === 0 ? null : 'Detach this picture from every item before deleting it.',
            'created_at' => $picture->created_at->toISOString(),
        ];
    }
}
