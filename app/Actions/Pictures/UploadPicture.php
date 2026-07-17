<?php

namespace App\Actions\Pictures;

use App\Enums\PictureProcessingStatus;
use App\Exceptions\PictureUploadException;
use App\Jobs\ProcessPictureDerivatives;
use App\Models\Picture;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Throwable;

class UploadPicture
{
    public function __construct(private GetPictureStorageStatus $getPictureStorageStatus) {}

    public function execute(User $user, UploadedFile $file, ?string $name = null): Picture
    {
        if (! $this->getPictureStorageStatus->isConfigured()) {
            throw new ServiceUnavailableHttpException(null, 'AWS photo storage is not configured. Photo uploads are unavailable.');
        }

        $uuid = (string) Str::uuid();
        $keyPrefix = 'users/'.$user->auth_uuid.'/pictures/'.$uuid;
        $disk = Storage::disk('pictures');
        $stagingKey = $keyPrefix.'/v1/staging/source';
        $stagingStored = false;

        try {
            $stagingStored = $disk->put($stagingKey, $file->getContent());
            $picture = DB::transaction(fn () => Picture::create([
                'uuid' => $uuid,
                'name' => $name ?: $file->getClientOriginalName(),
                'disk' => 'pictures',
                'key_prefix' => $keyPrefix,
                'processing_status' => PictureProcessingStatus::Pending,
                'mime_type' => $file->getMimeType(),
                'byte_size' => $file->getSize(),
                'user_id' => $user->id,
            ]));
        } catch (Throwable $exception) {
            if ($stagingStored) {
                $disk->delete($stagingKey);
            }

            throw new PictureUploadException($this->failureMessage($exception), $exception);
        }

        ProcessPictureDerivatives::dispatch($picture->id, $picture->processing_version)->afterCommit();

        return $picture;
    }

    private function failureMessage(Throwable $exception): string
    {
        if (
            $exception instanceof QueryException
            && ($exception->errorInfo[0] ?? (string) $exception->getCode()) === '42703'
        ) {
            return 'Photo uploads are unavailable because the application database is out of date.';
        }

        return 'The photo could not be uploaded. Please try again.';
    }
}
