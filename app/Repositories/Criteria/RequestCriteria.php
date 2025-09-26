<?php

namespace App\Repositories\Criteria;

use App\Repositories\Contracts\CriteriaInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RequestCriteria implements CriteriaInterface
{
    /**
     * The request instance
     */
    protected Request $request;

    /**
     * Create a new criteria instance
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply criteria to the query builder
     */
    public function apply(Builder $query): Builder
    {
        // Apply search criteria if search parameter exists
        if ($this->request->has('search')) {
            $search = $this->request->get('search');
            $searchableFields = $this->getSearchableFields();
            
            if (!empty($searchableFields)) {
                $query->where(function ($q) use ($search, $searchableFields) {
                    foreach ($searchableFields as $field) {
                        $q->orWhere($field, 'LIKE', "%{$search}%");
                    }
                });
            }
        }

        // Apply field-specific filters
        $filters = $this->request->except(['search', 'page', 'per_page', 'sort', 'order']);
        
        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, $value);
                }
            }
        }

        // Apply sorting
        if ($this->request->has('sort')) {
            $sort = $this->request->get('sort');
            $direction = $this->request->get('order', 'asc');
            
            if (in_array(strtolower($direction), ['asc', 'desc'])) {
                $query->orderBy($sort, $direction);
            }
        }

        return $query;
    }

    /**
     * Get searchable fields for the current model
     * Override this method in your repository to define searchable fields
     */
    protected function getSearchableFields(): array
    {
        return [];
    }
}
