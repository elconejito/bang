<?php

namespace App\Repositories;

use App\Models\Reference\BulletType;
use App\Repositories\Interfaces\BulletTypeRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class BulletTypeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BulletTypeRepositoryEloquent extends BaseRepository implements BulletTypeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BulletType::class;
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
