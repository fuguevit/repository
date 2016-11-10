<?php

namespace Fuguevit\Repositories\Console\Commands\Creators;

use Doctrine\Common\Inflector\Inflector;
use Illuminate\Filesystem\Filesystem;

/**
 * Class RepositoryCreator.
 */
class RepositoryCreator
{
    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var
     */
    protected $repository;

    /**
     * @var
     */
    protected $model;

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
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param mixed $repository
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Create the repository.
     *
     * @param $repository
     * @param $model
     *
     * @return int
     */
    public function create($repository, $model)
    {
        $this->setRepository($repository);
        $this->setModel($model);
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
        return config('repository.repository_path');
    }

    /**
     * Get the repository name.
     *
     * @return mixed|string
     */
    protected function getRepositoryName()
    {
        $repository_name = $this->getRepository();

        if (!strpos($repository_name, 'Repository') !== false) {
            $repository_name .= 'Repository';
        }

        return $repository_name;
    }

    /**
     * Get the model name.
     *
     * @return string
     */
    protected function getModelName()
    {
        $model = $this->getModel();

        if (isset($model) && !empty($model)) {
            $model_name = $model;
        } else {
            $model_name = Inflector::singularize($this->stripRepositoryName());
        }

        return $model_name;
    }

    /**
     * Get the stripped repository name.
     *
     * @return string
     */
    protected function stripRepositoryName()
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
        $repository_namespace = config('repository.repository_namespace');
        $repository_class = $this->getRepositoryName();
        $model_path = config('repository.model_namespace');
        $model_name = $this->getModelName();

        $populate_data = [
            'repository_namespace' => $repository_namespace,
            'repository_class'     => $repository_class,
            'model_path'           => $model_path,
            'model_name'           => $model_name,
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
        $path = $this->getDirectory().DIRECTORY_SEPARATOR.$this->getRepositoryName().'.php';

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

        $stub = $this->files->get($stub_path.'repository.stub');

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
