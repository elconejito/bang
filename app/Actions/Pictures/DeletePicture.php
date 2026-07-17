<?php

namespace App\Actions\Pictures;

use App\Jobs\DeletePictureObjects;
use App\Models\Picture;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class DeletePicture
{
    public function __construct(private GetPictureStorageStatus $getPictureStorageStatus) {}

    public function execute(Picture $picture): void
    {
        if (! $this->getPictureStorageStatus->isConfigured()) {
            throw new ServiceUnavailableHttpException(null, 'AWS photo storage is not configured. Permanent photo deletion is unavailable.');
        }

        DB::transaction(function () use ($picture): void {
            $picture->newQueryWithoutScopes()->whereKey($picture)->lockForUpdate()->firstOrFail();
            $hasAttachments = DB::table('cms.pictureables')->where('picture_id', $picture->id)->lockForUpdate()->exists()
                || DB::table('cms.targets')->where('picture_id', $picture->id)->lockForUpdate()->exists();
            if ($hasAttachments) {
                throw new ConflictHttpException('Detach this picture from every item before deleting it.');
            }
            $picture->delete();
        });

        $keys = array_map(fn (string $variant) => $picture->variantKey($variant), array_keys(Picture::VARIANTS));
        DeletePictureObjects::dispatch($picture->disk, array_merge([$picture->stagingKey()], $keys))->afterCommit();
    }
}
