<?php

namespace App\Repositories;

use App\Models\Store;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Criteria\RequestCriteria;
use App\Repositories\Interfaces\StoreRepository;

/**
 * Class StoreRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class StoreRepositoryEloquent extends BaseRepository implements StoreRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Store::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
