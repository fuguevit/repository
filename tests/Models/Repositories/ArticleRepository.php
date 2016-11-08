<?php

namespace Fuguevit\Repositories\Tests\Models\Repositories;

use Fuguevit\Repositories\Eloquent\Repository;

class ArticleRepository extends Repository
{
    public function model()
    {
        return 'Fuguevit\Repositories\Tests\Models\Article';
    }
}
