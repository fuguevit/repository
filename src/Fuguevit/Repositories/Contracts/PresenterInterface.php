<?php

namespace Fuguevit\Repositories\Contracts;

/**
 * Interface PresenterInterface.
 */
interface PresenterInterface
{
    /**
     * Prepare a new or cached presenter instance.
     *
     * @return mixed
     */
    public function present();
}
