<?php

namespace App\Repositories;

use App\Models\Magazine;
use App\Repositories\Interfaces\MagazineRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

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
    public function model()
    {
        return Magazine::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
