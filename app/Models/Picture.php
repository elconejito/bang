<?php

namespace App\Models;

use App\Actions\Pictures\GetPictureStorageStatus;
use App\Enums\PictureProcessingStatus;
use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Picture extends Model
{
    use HasFactory;

    public const VARIANTS = ['large' => 1920, 'card' => 800, 'thumbnail' => 320];

    protected $table = 'cms.pictures';

    protected $fillable = [
        'uuid', 'name', 'filename', 'disk', 'key_prefix', 'processing_status', 'processing_version',
        'mime_type', 'byte_size', 'width', 'height', 'failure_code', 'processed_at', 'user_id',
    ];

    protected $attributes = [
        'disk' => 'pictures',
        'processing_status' => 'pending',
        'processing_version' => 1,
    ];

    protected $casts = [
        'processing_status' => PictureProcessingStatus::class,
        'processed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new UserScope);
    }

    public function stagingKey(): string
    {
        return $this->key_prefix.'/v'.$this->processing_version.'/staging/source';
    }

    public function variantKey(string $variant): string
    {
        if (! array_key_exists($variant, self::VARIANTS)) {
            throw new \InvalidArgumentException('Unsupported picture variant.');
        }

        return $this->key_prefix.'/v'.$this->processing_version.'/'.$variant.'.webp';
    }

    public function temporaryUrl(string $variant, int $minutes = 10): ?string
    {
        if ($this->processing_status !== PictureProcessingStatus::Ready) {
            return null;
        }

        if (! resolve(GetPictureStorageStatus::class)->isConfigured()) {
            return null;
        }

        return Storage::disk($this->disk)->temporaryUrl($this->variantKey($variant), now()->addMinutes($minutes));
    }

    public function getUrl(string $size = 'thumbnail'): ?string
    {
        return $this->temporaryUrl($size === 'medium' ? 'card' : $size);
    }
}
