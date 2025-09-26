<?php

namespace App\Repositories;

use App\Models\Reference\Purpose;
use App\Repositories\Interfaces\PurposeRepository;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Criteria\RequestCriteria;

/**
 * Class PurposeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PurposeRepositoryEloquent extends BaseRepository implements PurposeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Purpose::class;
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
