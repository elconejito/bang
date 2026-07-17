<?php

namespace App\Actions\Pictures;

class GetPictureStorageStatus
{
    /**
     * @return array{driver: string, aws_configured: bool, uploads_enabled: bool, notice: string|null}
     */
    public function execute(): array
    {
        $disk = config('filesystems.disks.pictures', []);
        $driver = $disk['driver'] ?? 'local';
        $awsConfigured = collect(['key', 'secret', 'region', 'bucket'])
            ->every(fn (string $key): bool => filled($disk[$key] ?? null));

        return [
            'driver' => $driver,
            'aws_configured' => $awsConfigured,
            'uploads_enabled' => $awsConfigured,
            'notice' => $awsConfigured
                ? null
                : 'AWS photo storage is not configured. Photo uploads are unavailable.',
        ];
    }

    public function isConfigured(): bool
    {
        return $this->execute()['aws_configured'];
    }
}
