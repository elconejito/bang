<?php

namespace App\Repositories;

use App\Models\TrainingSession;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Criteria\RequestCriteria;
use App\Repositories\Interfaces\TrainingRepository;

/**
 * Class TrainingRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class TrainingRepositoryEloquent extends BaseRepository implements TrainingRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return TrainingSession::class;
    }

    /**
     * Boot up the repository, pushing criteria

     */
    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
