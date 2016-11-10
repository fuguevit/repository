<?php

namespace Fuguevit\Repositories\Console\Commands;

use Fuguevit\Repositories\Console\Commands\Creators\CriteriaCreator;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class MakeCriteriaCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'make:criteria';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Create a new criteria class';

    /**
     * @var
     */
    protected $creator;

    /**
     * @var
     */
    protected $composer;

    public function __construct(CriteriaCreator $creator)
    {
        parent::__construct();
        // Set the creator.
        $this->creator = $creator;
        // Set Composer.
        $this->composer = app()['composer'];
    }

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        // Get arguments.
        $arguments = $this->argument();
        // Create criteria.
        $this->createCriteria($arguments);
        // Run composer dump-autoload.
        $this->composer->dumpAutoloads();
    }

    /**
     * @param $arguments
     */
    protected function createCriteria($arguments)
    {
        // Set criteria.
        $criteria = $arguments['criteria'];
        // Create the criteria.
        if ($this->creator->create($criteria)) {
            $this->info('Create criteria class successfully!');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getArguments()
    {
        return [
            ['criteria', InputArgument::REQUIRED, 'Criteria Name.'],
        ];
    }
}
