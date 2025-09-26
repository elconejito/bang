<?php

namespace App\Repositories;

use App\Models\Reference\ShotMaterial;
use App\Repositories\Interfaces\ShotMaterialRepository;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Criteria\RequestCriteria;

/**
 * Class ShotMaterialRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ShotMaterialRepositoryEloquent extends BaseRepository implements ShotMaterialRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return ShotMaterial::class;
    }

    /**
     * Boot up the repository, pushing criteria
     *

     */
    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
