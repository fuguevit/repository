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

    protected $newModel;

    protected $criteria;

    protected $skipCriteria = false;

    protected $preventCriteriaOverwriting = true;

    public function __construct(App $app, Collection $collection)
    {
        $this->app = $app;
        $this->criteria = $collection;
        $this->resetScope();
        $this->makeModel();
    }

    /**
     * Specify Model class name.
     *
     * @return mixed
     */
    abstract public function model();

    public function makeModel()
    {
        return $this->setModel($this->model());
    }

    public function setModel($eloquentModel)
    {
        $this->newModel = $this->app->make($eloquentModel);

        if (!$this->newModel instanceof Model) {
            throw  new RepositoryException("Class {$this->newModel} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $this->newModel;
    }

    public function resetScope()
    {
        return $this->skipCriteria(false);
    }

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
            // Find existing criteria
            $key = $this->criteria->search(function ($item) use ($criteria) {
                return is_object($item) and (get_class($item) == get_class($criteria));
            });

            // Remove old criteria
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
     * @param array $columns
     *
     * @return mixed
     */
    public function all($columns = ['*'])
    {
        $this->applyCriteria();

        return $this->model->get($columns);
    }

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function with(array $relations)
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    /**
     * @param string $value
     * @param string $key
     *
     * @return array
     */
    public function lists($value, $key = null)
    {
        $this->applyCriteria();
        $lists = $this->model->lists($value, $key);
        if (is_array($lists)) {
            return $lists;
        }

        return $lists->all();
    }

    /**
     * @param int   $perPage
     * @param array $columns
     *
     * @return mixed
     */
    public function paginate($perPage = 25, $columns = ['*'])
    {
        $this->applyCriteria();

        return $this->model->paginate($perPage, $columns);
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * save a model without massive assignment.
     *
     * @param array $data
     *
     * @return bool
     */
    public function saveModel(array $data)
    {
        foreach ($data as $k => $v) {
            $this->model->$k = $v;
        }

        return $this->model->save();
    }

    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     *
     * @return mixed
     */
    public function update(array $data, $id, $attribute = 'id')
    {
        return $this->model->where($attribute, '=', $id)->update($data);
    }

    /**
     * @param array $data
     * @param  $id
     *
     * @return mixed
     */
    public function updateRich(array $data, $id)
    {
        if (!($model = $this->model->find($id))) {
            return false;
        }

        return $model->fill($data)->save();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * @param $id
     * @param array $columns
     *
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        $this->applyCriteria();

        return $this->model->find($id, $columns);
    }

    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     *
     * @return mixed
     */
    public function findBy($attribute, $value, $columns = ['*'])
    {
        $this->applyCriteria();

        return $this->model->where($attribute, '=', $value)->first($columns);
    }

    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     *
     * @return mixed
     */
    public function findAllBy($attribute, $value, $columns = ['*'])
    {
        $this->applyCriteria();

        return $this->model->where($attribute, '=', $value)->get($columns);
    }

    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     *
     * @return mixed
     */
    public function findAllExcept($attribute, $value, $columns = ['*'])
    {
        $this->applyCriteria();

        return $this->model->where($attribute, '!=', $value)->get($columns);
    }

    public function findWhere($where, $columns = ['*'], $or = false)
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
                //                if(array_search($field, array_keys($where))==0) {
//                    $model = $model->where($field, '=', $value);
//                } else {
                    $model = (!$or)
                        ? $model->where($field, '=', $value)
                        : $model->orWhere($field, '=', $value);
//                }
            }
        }

        return $model->get($columns);
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
        $res = $this->callMethod($method, $arguments);

        return $res;
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
