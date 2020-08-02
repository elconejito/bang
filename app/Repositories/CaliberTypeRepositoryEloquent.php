<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\CaliberTypeRepository;
use App\Models\Reference\CaliberType;

/**
 * Class CaliberTypeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CaliberTypeRepositoryEloquent extends BaseRepository implements CaliberTypeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return CaliberType::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
