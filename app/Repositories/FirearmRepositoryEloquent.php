<?php

namespace App\Repositories;

use App\Models\Firearm;
use App\Repositories\Interfaces\FirearmRepository;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Criteria\RequestCriteria;

/**
 * Class FirearmRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class FirearmRepositoryEloquent extends BaseRepository implements FirearmRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Firearm::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
