<?php

namespace App\Repositories;

use App\Models\Reference\BulletType;
use App\Repositories\Interfaces\BulletTypeRepository;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Criteria\RequestCriteria;

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
    public function model(): string
    {
        return BulletType::class;
    }

    /**
     * Boot up the repository, pushing criteria
     *

     */
    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
