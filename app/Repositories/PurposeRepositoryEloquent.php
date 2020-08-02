<?php

namespace App\Repositories;

use App\Models\Reference\Purpose;
use App\Repositories\Interfaces\PurposeRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class PurposeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PurposeRepositoryEloquent extends BaseRepository implements PurposeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Purpose::class;
    }


    /**
     * Boot up the repository, pushing criteria
     *
     * @throws RepositoryException
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
