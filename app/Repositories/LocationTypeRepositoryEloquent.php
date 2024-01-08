<?php

namespace App\Repositories;

use App\Models\Reference\LocationType;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\LocationTypeRepository;
use Prettus\Repository\Exceptions\RepositoryException;

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
     * @throws RepositoryException
     */
    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
