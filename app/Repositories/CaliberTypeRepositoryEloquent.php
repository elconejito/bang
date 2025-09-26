<?php

namespace App\Repositories;

use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Criteria\RequestCriteria;
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
    public function model(): string
    {
        return CaliberType::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
