<?php

namespace Fuguevit\Repositories\Console\Commands\Creators;

use Illuminate\Filesystem\Filesystem;

/**
 * Class CriteriaCreator.
 */
class CriteriaCreator
{
    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var
     */
    protected $criteria;

    /**
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * @return mixed
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param mixed $criteria
     */
    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * Create the repository.
     *
     * @param $criteria
     *
     * @return int
     */
    public function create($criteria)
    {
        $this->setCriteria($criteria);
        $this->createDirectory();

        return $this->createClass();
    }

    /**
     * Create repository directory if not exist.
     *
     * @return null
     */
    protected function createDirectory()
    {
        $directory = $this->getDirectory();
        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }
    }

    /**
     * Get the repository directory.
     *
     * @return mixed
     */
    protected function getDirectory()
    {
        return config('repository.criteria_path');
    }

    /**
     * Get the criteria name.
     *
     * @return mixed|string
     */
    protected function getCriteriaName()
    {
        $criteria_name = $this->getCriteria();

        if (!strpos($criteria_name, 'Criteria') !== false) {
            $criteria_name .= 'Criteria';
        }

        return $criteria_name;
    }

    /**
     * Get the stripped criteria name.
     *
     * @return string
     */
    protected function stripCriteriaName()
    {
        $repository = strtolower($this->getRepository());
        $stripped = str_replace('repository', '', $repository);
        $result = ucfirst($stripped);

        return $result;
    }

    /**
     * Get the populate data.
     *
     * @return array
     */
    protected function getPopulateData()
    {
        $criteria_namespace = config('repository.criteria_namespace');
        $criteria_class = $this->getCriteriaName();

        $populate_data = [
            'criteria_namespace' => $criteria_namespace,
            'criteria_class'     => $criteria_class,
        ];

        return $populate_data;
    }

    /**
     * Get the path.
     *
     * @return string
     */
    protected function getPath()
    {
        $path = $this->getDirectory().DIRECTORY_SEPARATOR.$this->getCriteriaName().'.php';

        return $path;
    }

    /**
     * Get the stub.
     *
     * @return string
     */
    protected function getStub()
    {
        // Stub path.
        $stub_path = __DIR__.'/../../../../resources/stubs/';

        $stub = $this->files->get($stub_path.'criteria.stub');

        return $stub;
    }

    /**
     * Populate the stub.
     *
     * @return mixed
     */
    protected function populateStub()
    {
        // Populate data
        $populate_data = $this->getPopulateData();

        // Stub
        $stub = $this->getStub();

        // Loop through the populate data.
        foreach ($populate_data as $key => $value) {
            // Populate the stub.
            $stub = str_replace($key, $value, $stub);
        }

        // Return the stub.
        return $stub;
    }

    /**
     * Put class file to the path.
     *
     * @return int
     */
    protected function createClass()
    {
        return $this->files->put($this->getPath(), $this->populateStub());
    }
}
