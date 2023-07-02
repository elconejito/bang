<?php

namespace App\Repositories;

use App\Models\Caliber;
use App\Repositories\Interfaces\CaliberRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

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
    public function model(): string
    {
        return Caliber::class;
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
