<?php

namespace App\Criteria;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class NotableTypeCriteria.
 *
 * @package namespace App\Criteria;
 */
class NotableTypeCriteria implements CriteriaInterface
{
    protected string $model_class;
    protected int $model_id;

    /**
     * @param $model_class
     * @param $model_id
     */
    public function __construct($model_class, $model_id)
    {
        $this->model_class = $model_class;
        $this->model_id = $model_id;
    }

    /**
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return Builder
     */
    public function apply($model, RepositoryInterface $repository): Builder
    {
        return $model->where('notable_type', $this->model_class)->where('notable_id', $this->model_id);
    }
}
