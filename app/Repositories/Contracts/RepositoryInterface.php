<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RepositoryInterface
{
    /**
     * Get all models
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * Find a model by its primary key
     */
    public function find(int $id, array $columns = ['*']): ?Model;

    /**
     * Find a model by its primary key or throw an exception
     */
    public function findOrFail(int $id, array $columns = ['*']): Model;

    /**
     * Create a new model instance
     */
    public function create(array $data): Model;

    /**
     * Update a model by its primary key
     */
    public function update(array $data, int $id): bool;

    /**
     * Delete a model by its primary key
     */
    public function delete(int $id): bool;

    /**
     * Set the relationships to be eager loaded
     */
    public function with(array $relations): self;

    /**
     * Add an order by clause
     */
    public function orderBy(string $column, string $direction = 'asc'): self;

    /**
     * Add criteria to the repository
     */
    public function pushCriteria(CriteriaInterface $criteria): self;

    /**
     * Paginate the results
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;
}
