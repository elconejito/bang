<?php

namespace App\Repositories;

use App\Models\Reference\PrimerType;
use App\Repositories\Interfaces\PrimerTypeRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class PrimerTypeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PrimerTypeRepositoryEloquent extends BaseRepository implements PrimerTypeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PrimerType::class;
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
