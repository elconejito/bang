<?php

namespace App\Actions\Pictures;

use App\Enums\PictureProcessingStatus;
use App\Jobs\ProcessPictureDerivatives;
use App\Models\Picture;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class UploadPicture
{
    public function execute(User $user, UploadedFile $file, ?string $name = null): Picture
    {
        $uuid = (string) Str::uuid();
        $keyPrefix = 'users/'.$user->auth_uuid.'/pictures/'.$uuid;
        $disk = Storage::disk('pictures');
        $stagingKey = $keyPrefix.'/v1/staging/source';
        $disk->put($stagingKey, $file->getContent(), ['visibility' => 'private']);

        try {
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
            $disk->delete($stagingKey);
            throw $exception;
        }

        ProcessPictureDerivatives::dispatch($picture->id, $picture->processing_version)->afterCommit();

        return $picture;
    }
}
