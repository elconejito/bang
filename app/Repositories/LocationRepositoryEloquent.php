<?php

namespace App\Repositories;

use App\Models\Location;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\LocationRepository;
use Prettus\Repository\Exceptions\RepositoryException;

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
     * @throws RepositoryException
     */
    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
