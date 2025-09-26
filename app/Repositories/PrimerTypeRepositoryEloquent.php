<?php

namespace App\Repositories;

use App\Models\Reference\PrimerType;
use App\Repositories\Interfaces\PrimerTypeRepository;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Criteria\RequestCriteria;

/**
 * Class PrimerTypeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PrimerTypeRepositoryEloquent extends BaseRepository implements PrimerTypeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return PrimerType::class;
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
