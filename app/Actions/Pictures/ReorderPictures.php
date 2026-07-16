<?php

namespace App\Actions\Pictures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReorderPictures
{
    public function execute(Model $entity, array $ids): void
    {
        DB::transaction(function () use ($entity, $ids): void {
            $current = $entity->pictures()->lockForUpdate()->pluck('cms.pictures.id')->map(fn ($id) => (int) $id)->sort()->values()->all();
            $requested = collect($ids)->map(fn ($id) => (int) $id)->sort()->values()->all();
            if ($current !== $requested) {
                throw ValidationException::withMessages(['ids' => 'IDs must contain every attached picture exactly once.']);
            }
            foreach ($ids as $index => $id) {
                $entity->pictures()->updateExistingPivot($id, ['sort_order' => $index]);
            }
        });
    }
}
