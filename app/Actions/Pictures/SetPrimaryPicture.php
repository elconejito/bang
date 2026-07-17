<?php

namespace App\Actions\Pictures;

use App\Models\Picture;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SetPrimaryPicture
{
    public function execute(Model $entity, Picture $picture): void
    {
        DB::transaction(function () use ($entity, $picture): void {
            $pictures = $entity->pictures()->lockForUpdate()->get();
            if (! $pictures->contains('id', $picture->id)) {
                throw new NotFoundHttpException;
            }
            DB::table('cms.pictureables')
                ->where('pictureable_type', $entity->getMorphClass())
                ->where('pictureable_id', $entity->getKey())
                ->update(['is_primary' => false]);
            $entity->pictures()->updateExistingPivot($picture->id, ['is_primary' => true]);
        });
    }
}
