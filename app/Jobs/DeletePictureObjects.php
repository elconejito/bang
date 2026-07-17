<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DeletePictureObjects implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public int $timeout = 60;

    public array $backoff = [10, 60, 300, 900];

    public function __construct(public string $disk, public array $keys) {}

    public function handle(): void
    {
        Storage::disk($this->disk)->delete($this->keys);
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Picture object cleanup failed.', [
            'disk' => $this->disk,
            'key_count' => count($this->keys),
            'exception' => $exception,
        ]);
    }
}
