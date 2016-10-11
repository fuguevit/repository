<?php

namespace Fuguevit\Repositories\Contracts;

use Fuguevit\Repositories\Criteria\Criteria;

/**
 * Interface CriteriaInterface.
 */
interface CriteriaInterface
{
    /**
     * @param bool $status
     *
     * @return $this
     */
    public function skipCriteria($status = true);

    /**
     * @return mixed
     */
    public function getCriteria();

    /**
     * @param Criteria $criteria
     *
     * @return $this
     */
    public function getByCriteria(Criteria $criteria);

    /**
     * @param Criteria $criteria
     *
     * @return $this
     */
    public function pushCriteria(Criteria $criteria);

    /**
     * @return $this
     */
    public function applyCriteria();
}
