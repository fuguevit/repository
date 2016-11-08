<?php

namespace Fuguevit\Repositories\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';

    protected $fillable = ['title', 'body'];
}
