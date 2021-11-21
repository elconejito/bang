<?php

namespace App\Repositories;

use App\Models\Firearm;
use App\Repositories\Interfaces\FirearmRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class FirearmRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class FirearmRepositoryEloquent extends BaseRepository implements FirearmRepository
{
    protected $fieldSearchable = [
        'calibers.id'
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Firearm::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
