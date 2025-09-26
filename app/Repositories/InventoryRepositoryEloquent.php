<?php

namespace App\Repositories;

use App\Models\Inventory;
use App\Repositories\Interfaces\InventoryRepository;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Criteria\RequestCriteria;

/**
 * Class InventoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class InventoryRepositoryEloquent extends BaseRepository implements InventoryRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Inventory::class;
    }

    /**
     * Boot up the repository, pushing criteria

     */
    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
