<?php

namespace Fuguevit\Repositories\Tests;

use Fuguevit\Repositories\Tests\Models\Article;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    public function setUp()
    {
        parent::setUp();

        Schema::create('articles', function ($table) {
            $table->increments('id');
            $table->string('title');
            $table->text('body');
            $table->timestamps();
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['path.base'] = __DIR__.'/../src';
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app)
    {
        return [
            'Fuguevit\Repositories\Providers\RepositoryServiceProvider',
        ];
    }

    /**
     * Create articles.
     *
     * @param $number
     */
    protected function createArticles($number)
    {
        for ($i = 0; $i < $number; $i++) {
            Article::create([
                'title' => 'title-'.$i,
                'body'  => str_random(100),
            ]);
        }
    }
}
