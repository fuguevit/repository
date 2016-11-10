<?php

namespace Fuguevit\Repositories\Providers;

use Fuguevit\Repositories\Console\Commands\Creators\CriteriaCreator;
use Fuguevit\Repositories\Console\Commands\Creators\RepositoryCreator;
use Fuguevit\Repositories\Console\Commands\MakeCriteriaCommand;
use Fuguevit\Repositories\Console\Commands\MakeRepositoryCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/repository.php' => config_path('repository.php'),
        ], 'config');
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Register binds.
        $this->registerBindings();
        // Register make repository command.
        $this->registerMakeRepositoryCommand();
        // Register make criteria command.
        $this->registerMakeCriteriaCommand();
        // Register commands.
        $this->commands(['command.repository.make', 'command.criteria.make']);

        $config_path = __DIR__.'/../../config/repository.php';
        // Merge config
        $this->mergeConfigFrom($config_path, 'repository');
    }

    /**
     * Register the bindings. Pre-requisite for register make repository command.
     */
    protected function registerBindings()
    {
        // FileSystem
        $this->app->instance('FileSystem', new Filesystem());

        // Composer
        $this->app->bind('Composer', function ($app) {
            return new Composer($app['FileSystem']);
        });

        // RepositoryCreator
        $this->app->singleton('RepositoryCreator', function ($app) {
            return new RepositoryCreator($app['FileSystem']);
        });

        // CriteriaCreator
        $this->app->singleton('CriteriaCreator', function ($app) {
            return new CriteriaCreator($app['FileSystem']);
        });
    }

    /**
     * Register the make:repository command.
     */
    protected function registerMakeRepositoryCommand()
    {
        // Make repository command.
        $this->app['command.repository.make'] = $this->app->share(
            function ($app) {
                return new MakeRepositoryCommand($app['RepositoryCreator']);
            }
        );
    }

    /**
     * Register the make:criteria command.
     */
    protected function registerMakeCriteriaCommand()
    {
        // Make criteria command.
        $this->app['command.criteria.make'] = $this->app->share(
            function ($app) {
                return new MakeCriteriaCommand($app['CriteriaCreator']);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function provides()
    {
        return [
            'command.repository.make',
            'command.criteria.make',
        ];
    }
}
