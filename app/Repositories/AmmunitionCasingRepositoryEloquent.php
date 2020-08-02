<?php

namespace App\Repositories;

use App\Models\Reference\AmmunitionCasing;
use App\Repositories\Interfaces\AmmunitionCasingRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

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
    public function model()
    {
        return AmmunitionCasing::class;
    }


    /**
     * Boot up the repository, pushing criteria
     *
     * @throws RepositoryException
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
