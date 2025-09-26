<?php

namespace App\Repositories;

use App\Models\Reference\ShellLength;
use App\Repositories\Interfaces\ShellLengthRepository;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Criteria\RequestCriteria;

/**
 * Class ShellLengthRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ShellLengthRepositoryEloquent extends BaseRepository implements ShellLengthRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return ShellLength::class;
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
