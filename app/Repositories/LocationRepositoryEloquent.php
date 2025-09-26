<?php

namespace App\Repositories;

use App\Models\Location;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Criteria\RequestCriteria;
use App\Repositories\Interfaces\LocationRepository;

/**
 * Class LocationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class LocationRepositoryEloquent extends BaseRepository implements LocationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Location::class;
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
