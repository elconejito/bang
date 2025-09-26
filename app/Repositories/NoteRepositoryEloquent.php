<?php

namespace App\Repositories;

use App\Models\Note;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Criteria\RequestCriteria;
use App\Repositories\Interfaces\NoteRepository;

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

     */
    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
