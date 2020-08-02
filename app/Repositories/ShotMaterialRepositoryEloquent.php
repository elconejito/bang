<?php

namespace App\Repositories;

use App\Models\Reference\ShotMaterial;
use App\Repositories\Interfaces\ShotMaterialRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class ShotMaterialRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ShotMaterialRepositoryEloquent extends BaseRepository implements ShotMaterialRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ShotMaterial::class;
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
