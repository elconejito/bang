<?php

namespace App\Repositories;

use App\Models\Inventory;
use App\Repositories\Interfaces\InventoryRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class InventoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class InventoryRepositoryEloquent extends BaseRepository implements InventoryRepository
{
    protected $fieldSearchable = [
        'ammunition_id',
        'training_session_id',
        'firearm_id',
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Inventory::class;
    }

    /**
     * Boot up the repository, pushing criteria
     * @throws RepositoryException
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
