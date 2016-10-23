<?php

namespace Fuguevit\Repositories\Criteria;

use Fuguevit\Repositories\Contracts\RepositoryInterface as Repository;

abstract class Criteria
{
    /**
     * @param model
     * @param Repository $repository
     *
     * @return mixed
     */
    abstract public function apply($model, Repository $repository);
}
