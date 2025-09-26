<?php

namespace App\Repositories;

use App\Models\Caliber;
use App\Repositories\Interfaces\CaliberRepository;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Criteria\RequestCriteria;

/**
 * Class CaliberRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CaliberRepositoryEloquent extends BaseRepository implements CaliberRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Caliber::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
