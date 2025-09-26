<?php

namespace App\Repositories;

use App\Models\Reference\AmmunitionCasing;
use App\Repositories\Interfaces\AmmunitionCasingRepository;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Criteria\RequestCriteria;

/**
 * Class AmmunitionCasingRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AmmunitionCasingRepositoryEloquent extends BaseRepository implements AmmunitionCasingRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return AmmunitionCasing::class;
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
