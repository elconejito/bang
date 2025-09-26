<?php

namespace App\Repositories;

use App\Models\Reference\LocationType;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Criteria\RequestCriteria;
use App\Repositories\Interfaces\LocationTypeRepository;

/**
 * Class LocationTypeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class LocationTypeRepositoryEloquent extends BaseRepository implements LocationTypeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return LocationType::class;
    }

    /**
     * Boot up the repository, pushing criteria

     */
    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
