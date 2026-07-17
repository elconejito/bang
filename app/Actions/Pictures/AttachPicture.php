<?php

namespace App\Actions\Pictures;

use App\Models\Picture;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttachPicture
{
    public function execute(Model $entity, Picture $picture): void
    {
        DB::transaction(function () use ($entity, $picture): void {
            $pictures = $entity->pictures()->lockForUpdate()->get();
            if ($pictures->contains('id', $picture->id)) {
                return;
            }
            $entity->pictures()->attach($picture->id, [
                'user_id' => Auth::id(),
                'sort_order' => $pictures->count(),
                'is_primary' => $pictures->isEmpty(),
            ]);
        });
    }
}
