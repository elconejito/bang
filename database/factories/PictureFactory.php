<?php

namespace Database\Factories;

use App\Enums\PictureProcessingStatus;
use App\Models\Picture;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PictureFactory extends Factory
{
    protected $model = Picture::class;

    public function definition(): array
    {
        $uuid = (string) Str::uuid();

        return [
            'uuid' => $uuid,
            'name' => fake()->words(3, true).'.jpg',
            'disk' => 'pictures',
            'key_prefix' => 'users/'.Str::uuid().'/pictures/'.$uuid,
            'processing_status' => PictureProcessingStatus::Ready,
            'processing_version' => 1,
            'mime_type' => 'image/jpeg',
            'byte_size' => 1024,
            'width' => 800,
            'height' => 600,
            'processed_at' => now(),
            'user_id' => User::factory(),
        ];
    }
}
