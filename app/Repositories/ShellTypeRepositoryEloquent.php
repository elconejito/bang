<?php

namespace App\Repositories;

use App\Models\Reference\ShellType;
use App\Repositories\Interfaces\ShellTypeRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class ShellTypeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ShellTypeRepositoryEloquent extends BaseRepository implements ShellTypeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ShellType::class;
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
