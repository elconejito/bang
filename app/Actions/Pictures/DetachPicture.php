<?php

namespace App\Actions\Pictures;

use App\Models\Picture;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DetachPicture
{
    public function execute(Model $entity, Picture $picture): void
    {
        DB::transaction(function () use ($entity, $picture): void {
            $pictures = $entity->pictures()->lockForUpdate()->get();
            $attached = $pictures->firstWhere('id', $picture->id);
            if (! $attached) {
                throw new NotFoundHttpException;
            }
            if ($pictures->count() > 1 && $attached->pivot->is_primary) {
                throw new ConflictHttpException('Choose another primary picture before removing this one.');
            }
            $entity->pictures()->detach($picture->id);
            $entity->pictures()->get()->values()->each(fn (Picture $item, int $index) => $entity->pictures()->updateExistingPivot($item->id, ['sort_order' => $index]));
        });
    }
}
