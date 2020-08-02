<?php

namespace App\Repositories;

use App\Models\Reference\ShellLength;
use App\Repositories\Interfaces\ShellLengthRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class ShellLengthRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ShellLengthRepositoryEloquent extends BaseRepository implements ShellLengthRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ShellLength::class;
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
