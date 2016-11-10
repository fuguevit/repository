<?php

namespace Fuguevit\Repositories\Console\Commands;

use Fuguevit\Repositories\Console\Commands\Creators\RepositoryCreator;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class MakeRepositoryCommand.
 */
class MakeRepositoryCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'make:repository';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Create a new repository class';

    /**
     * @var RepositoryCreator
     */
    protected $creator;

    /**
     * @var
     */
    protected $composer;

    /**
     * @param RepositoryCreator $creator
     */
    public function __construct(RepositoryCreator $creator)
    {
        parent::__construct();
        // Set the creator.
        $this->creator = $creator;
        // Set composer.
        $this->composer = app()['composer'];
    }

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        // Get arguments.
        $arguments = $this->argument();
        // Get options.
        $options = $this->option();
        // Create new repository.
        $this->createRepository($arguments, $options);
        // Run composer dump-autoload
        $this->composer->dumpAutoloads();
    }

    /**
     * @param $arguments
     * @param $options
     */
    protected function createRepository($arguments, $options)
    {
        // Set repository.
        $repository = $arguments['repository'];
        // Set model.
        $model = $options['model'];
        // Create the repository.
        if ($this->creator->create($repository, $model)) {
            $this->info('Create repository class successfully!');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getArguments()
    {
        return [
            ['repository', InputArgument::REQUIRED, 'Repository name.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getOptions()
    {
        return [
            ['model', null, InputOption::VALUE_OPTIONAL, 'Model name.', null],
        ];
    }
}
