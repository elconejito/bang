<?php

namespace App\Repositories;

use App\Models\TrainingSession;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\TrainingRepository;
use Prettus\Repository\Exceptions\RepositoryException;

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
     * @throws RepositoryException
     */
    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
