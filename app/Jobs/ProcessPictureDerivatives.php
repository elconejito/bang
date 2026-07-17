<?php

namespace App\Jobs;

use App\Enums\PictureProcessingStatus;
use App\Models\Picture;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Throwable;

class ProcessPictureDerivatives implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 120;

    public array $backoff = [10, 60, 300];

    public function __construct(public int $pictureId, public int $version) {}

    public function uniqueId(): string
    {
        return $this->pictureId.':'.$this->version;
    }

    public function handle(): void
    {
        $picture = Picture::withoutGlobalScopes()->find($this->pictureId);
        if (! $picture || $picture->processing_version !== $this->version || $picture->processing_status === PictureProcessingStatus::Ready) {
            return;
        }

        $disk = Storage::disk($picture->disk);
        $picture->forceFill(['processing_status' => PictureProcessingStatus::Processing, 'failure_code' => null])->save();
        $manager = new ImageManager(new Driver);
        $source = $manager->read($disk->get($picture->stagingKey()))->orient();

        foreach (Picture::VARIANTS as $variant => $maximumEdge) {
            $image = clone $source;
            $image->scaleDown(width: $maximumEdge, height: $maximumEdge);
            $disk->put($picture->variantKey($variant), (string) $image->toWebp(85), ['visibility' => 'private']);
        }

        $picture->forceFill([
            'processing_status' => PictureProcessingStatus::Ready,
            'width' => $source->width(),
            'height' => $source->height(),
            'processed_at' => now(),
        ])->save();
        $disk->delete($picture->stagingKey());
    }

    public function failed(?Throwable $exception): void
    {
        $picture = Picture::withoutGlobalScopes()->find($this->pictureId);
        if (! $picture || $picture->processing_version !== $this->version) {
            return;
        }

        $disk = Storage::disk($picture->disk);
        $disk->delete(array_merge([$picture->stagingKey()], array_map(fn (string $variant) => $picture->variantKey($variant), array_keys(Picture::VARIANTS))));
        $picture->forceFill(['processing_status' => PictureProcessingStatus::Failed, 'failure_code' => 'processing_failed'])->save();
    }
}
