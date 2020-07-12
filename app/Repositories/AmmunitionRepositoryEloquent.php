<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
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
    public function model()
    {
        return Ammunition::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
