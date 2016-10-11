<?php

namespace Fuguevit\Repositories\Contracts;

interface CacheInterface
{
    /**
     * @param $minutes
     *
     * @return mixed
     */
    public function setTtl($minutes);

    /**
     * @param $bool
     *
     * @return mixed
     */
    public function setEnabled($bool);
}
