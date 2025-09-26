<?php

namespace App\Repositories;

use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Criteria\RequestCriteria;
use App\Repositories\Interfaces\AmmunitionRepository;
use App\Models\Ammunition;

/**
 * Class AmmunitionRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AmmunitionRepositoryEloquent extends BaseRepository implements AmmunitionRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Ammunition::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
