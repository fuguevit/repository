<?php

namespace Fuguevit\Repositories\Contracts;

/**
 * Interface RepositoryInterface.
 */
interface RepositoryInterface
{
    /**
     * Retrieve all entities.
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function all($columns = ['*']);

    /**
     * List entities by key/value.
     *
     * @param $value
     * @param null $key
     *
     * @return mixed
     */
    public function lists($value, $key = null);

    /**
     * Save a new entity in repository.
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function create(array $attributes);

    /**
     * Save a new entity without massive assignment.
     *
     * @param array $attributes
     *
     * @return bool
     */
    public function save(array $attributes);

    /**
     * Update an entity.
     *
     * @param array $attributes
     * @param $id
     *
     * @return mixed
     */
    public function update(array $attributes, $id);

    /**
     * Destroy an entity by id.
     *
     * @param $id
     *
     * @return bool
     */
    public function delete($id);

    /**
     * Get an entity by id.
     *
     * @param $id
     * @param array $columns
     *
     * @return mixed
     */
    public function find($id, $columns = ['*']);

    /**
     * Get an entity by value of specified field.
     *
     * @param $field
     * @param $value
     * @param array $columns
     *
     * @return mixed
     */
    public function findBy($field, $value, $columns = ['*']);

    /**
     * Get all entities that satisfied with the field value.
     *
     * @param $field
     * @param $value
     * @param array $columns
     *
     * @return mixed
     */
    public function findAllBy($field, $value, $columns = ['*']);

    /**
     * Get all entities that doesn't satisfy with the field value.
     *
     * @param $field
     * @param $value
     * @param array $columns
     *
     * @return mixed
     */
    public function findAllExcept($field, $value, $columns = ['*']);

    /**
     * Get entities by multiple fields in one field.
     *
     * @param $field
     * @param array $values
     * @param array $columns
     *
     * @return mixed
     */
    public function findIn($field, array $values, $columns = ['*']);

    /**
     * Get entities exclude multiple fields in one field.
     *
     * @param $field
     * @param array $values
     * @param array $columns
     *
     * @return mixed
     */
    public function findNotIn($field, array $values, $columns = ['*']);

    /**
     * Get entities by where clause.
     *
     * @param $where
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhere($where, $columns = ['*']);

    /**
     * Paginate entities.
     *
     * @param $perPage
     * @param array $columns
     *
     * @return mixed
     */
    public function paginate($perPage = 1, $columns = ['*']);

    /**
     * Order entities by a given column.
     *
     * @param $field
     * @param string $direction
     *
     * @return mixed
     */
    public function orderBy($field, $direction = 'asc');

    /**
     * Load relations.
     *
     * @param $relations
     *
     * @return mixed
     */
    public function with(array $relations);
}
