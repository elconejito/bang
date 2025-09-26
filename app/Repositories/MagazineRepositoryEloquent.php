<?php

namespace App\Repositories;

use App\Models\Magazine;
use App\Repositories\Interfaces\MagazineRepository;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Criteria\RequestCriteria;

/**
 * Class MagazineRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class MagazineRepositoryEloquent extends BaseRepository implements MagazineRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Magazine::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
