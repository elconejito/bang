<?php

namespace App\QueryFilters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FiltersLifecycleStatus implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        match (strtolower((string) $value)) {
            'archived' => $query->whereNotNull('archived_at'),
            'all' => null,
            default => $query->whereNull('archived_at'),
        };
    }
}
