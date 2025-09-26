<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Contracts\CriteriaInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements RepositoryInterface
{
    /**
     * The model instance
     */
    protected Model $model;

    /**
     * The query builder instance
     */
    protected Builder $query;

    /**
     * The relationships to be eager loaded
     */
    protected array $with = [];

    /**
     * The order by clauses
     */
    protected array $orderBy = [];

    /**
     * The criteria to be applied
     */
    protected array $criteria = [];

    /**
     * Create a new repository instance
     */
    public function __construct()
    {
        $this->makeModel();
        $this->resetQuery();
    }

    /**
     * Specify the model class name
     */
    abstract public function model(): string;

    /**
     * Make a new model instance
     */
    protected function makeModel(): void
    {
        $model = app($this->model());
        
        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }
        
        $this->model = $model;
    }

    /**
     * Reset the query builder
     */
    protected function resetQuery(): void
    {
        $this->query = $this->model->newQuery();
        $this->with = [];
        $this->orderBy = [];
        $this->criteria = [];
    }

    /**
     * Get all models
     */
    public function all(array $columns = ['*']): Collection
    {
        $this->applyCriteria();
        $this->applyWith();
        $this->applyOrderBy();
        
        $results = $this->query->get($columns);
        $this->resetQuery();
        
        return $results;
    }

    /**
     * Find a model by its primary key
     */
    public function find(int $id, array $columns = ['*']): ?Model
    {
        $this->applyCriteria();
        $this->applyWith();
        
        $result = $this->query->find($id, $columns);
        $this->resetQuery();
        
        return $result;
    }

    /**
     * Find a model by its primary key or throw an exception
     */
    public function findOrFail(int $id, array $columns = ['*']): Model
    {
        $this->applyCriteria();
        $this->applyWith();
        
        $result = $this->query->findOrFail($id, $columns);
        $this->resetQuery();
        
        return $result;
    }

    /**
     * Create a new model instance
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update a model by its primary key
     */
    public function update(array $data, int $id): bool
    {
        $model = $this->findOrFail($id);
        return $model->update($data);
    }

    /**
     * Delete a model by its primary key
     */
    public function delete(int $id): bool
    {
        $model = $this->findOrFail($id);
        return $model->delete();
    }

    /**
     * Set the relationships to be eager loaded
     */
    public function with(array $relations): self
    {
        $this->with = array_merge($this->with, $relations);
        return $this;
    }

    /**
     * Add an order by clause
     */
    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $this->orderBy[] = compact('column', 'direction');
        return $this;
    }

    /**
     * Add criteria to the repository
     */
    public function pushCriteria(CriteriaInterface $criteria): self
    {
        $this->criteria[] = $criteria;
        return $this;
    }

    /**
     * Paginate the results
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        $this->applyCriteria();
        $this->applyWith();
        $this->applyOrderBy();
        
        $results = $this->query->paginate($perPage, $columns);
        $this->resetQuery();
        
        return $results;
    }

    /**
     * Apply criteria to the query
     */
    protected function applyCriteria(): void
    {
        foreach ($this->criteria as $criteria) {
            $this->query = $criteria->apply($this->query);
        }
    }

    /**
     * Apply eager loading to the query
     */
    protected function applyWith(): void
    {
        if (!empty($this->with)) {
            $this->query->with($this->with);
        }
    }

    /**
     * Apply order by clauses to the query
     */
    protected function applyOrderBy(): void
    {
        foreach ($this->orderBy as $order) {
            $this->query->orderBy($order['column'], $order['direction']);
        }
    }
}
