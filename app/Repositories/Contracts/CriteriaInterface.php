<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface CriteriaInterface
{
    /**
     * Apply criteria to the query builder
     */
    public function apply(Builder $query): Builder;
}
