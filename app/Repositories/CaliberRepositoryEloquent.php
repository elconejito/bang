<?php

namespace App\Repositories;

use App\Models\Caliber;
use App\Repositories\Interfaces\CaliberRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class CaliberRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CaliberRepositoryEloquent extends BaseRepository implements CaliberRepository
{
    protected $fieldSearchable = [];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Caliber::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
