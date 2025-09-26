<?php

namespace App\Repositories;

use App\Models\Reference\AmmunitionCondition;
use App\Repositories\Interfaces\AmmunitionConditionRepository;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Criteria\RequestCriteria;

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
    public function model(): string
    {
        return AmmunitionCondition::class;
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
