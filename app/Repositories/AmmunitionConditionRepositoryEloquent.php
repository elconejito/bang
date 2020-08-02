<?php

namespace App\Repositories;

use App\Models\Reference\AmmunitionCondition;
use App\Repositories\Interfaces\AmmunitionConditionRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class AmmunitionConditionRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AmmunitionConditionRepositoryEloquent extends BaseRepository implements AmmunitionConditionRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AmmunitionCondition::class;
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
