<?php

namespace App\Repositories;

use App\Models\Note;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\NoteRepository;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class NoteRepositoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class NoteRepositoryEloquent extends BaseRepository implements NoteRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Note::class;
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
