<?php

namespace App\Repositories;

use App\Models\Reference\ShellType;
use App\Repositories\Interfaces\ShellTypeRepository;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Criteria\RequestCriteria;

/**
 * Class ShellTypeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ShellTypeRepositoryEloquent extends BaseRepository implements ShellTypeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return ShellType::class;
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
