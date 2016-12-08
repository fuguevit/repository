<?php

namespace Fuguevit\Repositories\Eloquent;

use Fuguevit\Repositories\Contracts\CriteriaInterface;
use Fuguevit\Repositories\Contracts\RepositoryInterface;
use Fuguevit\Repositories\Criteria\Criteria;
use Fuguevit\Repositories\Exceptions\RepositoryException;
use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class Repository implements CriteriaInterface, RepositoryInterface
{
    private $app;

    protected $model;

    protected $criteria;

    protected $skipCriteria = false;

    protected $preventCriteriaOverwriting = true;

    public function __construct(App $app, Collection $collection)
    {
        $this->app = $app;
        $this->criteria = $collection;
        $this->makeModel();
    }

    /**
     * Specify Model class name.
     *
     * @return mixed
     */
    abstract public function model();

    /**
     * @throws RepositoryException
     *
     * @return Model
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());
        if (!$model instanceof Model) {
            throw new RepositoryException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * @throws RepositoryException
     */
    public function resetModel()
    {
        $this->makeModel();
    }

    /**
     * @param bool $status
     *
     * @return $this
     */
    public function skipCriteria($status = true)
    {
        $this->skipCriteria = $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * Reset Criteria.
     */
    public function resetCriteria()
    {
        $this->criteria = new Collection();
    }

    /**
     * @param Criteria $criteria
     *
     * @return $this
     */
    public function getByCriteria(Criteria $criteria)
    {
        $this->model = $criteria->apply($this->model, $this);

        return $this;
    }

    /**
     * @param Criteria $criteria
     *
     * @return $this
     */
    public function pushCriteria(Criteria $criteria)
    {
        if ($this->preventCriteriaOverwriting) {
            $key = $this->criteria->search(function ($item) use ($criteria) {
                return is_object($item) and (get_class($item) == get_class($criteria));
            });

            if (is_int($key)) {
                $this->criteria->offsetUnset($key);
            }
        }

        $this->criteria->push($criteria);

        return $this;
    }

    /**
     * @return $this
     */
    public function applyCriteria()
    {
        if ($this->skipCriteria === true) {
            return $this;
        }

        foreach ($this->getCriteria() as $criteria) {
            if ($criteria instanceof Criteria) {
                $this->model = $criteria->apply($this->model, $this);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function all($columns = ['*'])
    {
        $this->applyCriteria();
        $result = $this->model->get($columns);

        $this->resetModel();

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function lists($value, $key = null)
    {
        $this->applyCriteria();
        $lists = $this->model->lists($value, $key);

        return $lists->all();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $data)
    {
        foreach ($data as $k => $v) {
            $this->model->$k = $v;
        }

        return $this->model->save();
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $data, $id, $attribute = 'id')
    {
        $model = $this->model->findOrFail($id);
        $model->fill($data);
        $model->save();

        $this->resetModel();

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * {@inheritdoc}
     */
    public function find($id, $columns = ['*'])
    {
        $this->applyCriteria();

        $model = $this->model->find($id, $columns);
        $this->resetModel();

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function findBy($field, $value, $columns = ['*'])
    {
        $this->applyCriteria();

        $model = $this->model->where($field, '=', $value)->first($columns);
        $this->resetModel();

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllBy($field, $value, $perPage = null, $columns = ['*'])
    {
        return $this->findWhere([$field => $value], $perPage, $columns);
    }

    /**
     * {@inheritdoc}
     */
    public function findAllExcept($field, $value, $perPage = null, $columns = ['*'])
    {
        return $this->findWhere([$field => [$field, '!=', $value]], $perPage, $columns);
    }

    /**
     * {@inheritdoc}
     */
    public function findIn($field, array $values, $perPage = null, $columns = ['*'])
    {
        $this->applyCriteria();

        $model = $this->model->whereIn($field, $values);
        if ($perPage) {
            $collection = $model->paginate($perPage, $columns);
        } else {
            $collection = $model->get($columns);
        }
        $this->resetModel();

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function findNotIn($field, array $values, $perPage = null, $columns = ['*'])
    {
        $this->applyCriteria();

        $model = $this->model->whereNotIn($field, $values);
        if ($perPage) {
            $collection = $model->paginate($perPage, $columns);
        } else {
            $collection = $model->get($columns);
        }
        $this->resetModel();

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function findWhere($where, $perPage = null, $columns = ['*'], $or = false)
    {
        $this->applyCriteria();
        $model = $this->model;

        foreach ($where as $field => $value) {
            if ($value instanceof \Closure) {
                $model = (!$or)
                    ? $model->where($value)
                    : $model->orWhere($value);
            } elseif (is_array($value)) {
                if (count($value) === 4) {
                    list($field, $operator, $search, $temp_or) = $value;
                    $model = (!$temp_or)
                        ? $model->where($field, $operator, $search)
                        : $model->orWhere($field, $operator, $search);
                } elseif (count($value) === 3) {
                    list($field, $operator, $search) = $value;
                    switch ($operator) {
                        case 'in':
                            $model = (!$or)
                                ? $model->whereIn($field, $search)  //$search (array)
                                : $model->orWhere(function ($query) use ($field, $search) {
                                    $query->whereIn($field, $search);
                                });
                            break;
                        default:
                            $model = (!$or)
                                ? $model->where($field, $operator, $search)
                                : $model->orWhere($field, $operator, $search);
                            break;
                    }
                } elseif (count($value) === 2) {
                    list($field, $search) = $value;
                    $model = (!$or)
                        ? $model->where($field, '=', $search)
                        : $model->orWhere($field, '=', $search);
                }
            } else {
                $model = (!$or)
                    ? $model->where($field, '=', $value)
                    : $model->orWhere($field, '=', $value);
            }
        }

        if (is_null($perPage)) {
            $collection = $model->get($columns);
        } else {
            $collection = $model->paginate($perPage, $columns);
        }

        $this->resetModel();

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function paginate($perPage = 25, $columns = ['*'])
    {
        $this->applyCriteria();
        $collection = $this->model->paginate($perPage, $columns);
        $this->resetModel();

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function orderBy($field, $direction = 'asc')
    {
        $this->model = $this->model->orderBy($field, $direction);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function with(array $relations)
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    /**
     * Load 'whereHas' relation with closure.
     *
     * @param $relation
     * @param $closure
     *
     * @return $this
     */
    public function whereHas($relation, $closure)
    {
        $this->model = $this->model->whereHas($relation, $closure);

        return $this;
    }

    /**
     * Load 'whereDoesntHave' relation with closure.
     *
     * @param $relation
     * @param $closure
     *
     * @return $this
     */
    public function whereDoesntHave($relation, $closure)
    {
        $this->model = $this->model->whereDoesntHave($relation, $closure);

        return $this;
    }

    /**
     * @param $method
     * @param $arguments
     *
     * @throws BadMethodCallException
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $this->applyCriteria();

        return $this->callMethod($method, $arguments);
    }

    /**
     * @param $method
     * @param $arguments
     *
     * @throws BadMethodCallException
     *
     * @return mixed
     */
    public function callMethod($method, $arguments)
    {
        return call_user_func_array([$this->model, $method], $arguments);
    }
}
