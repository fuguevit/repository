<?php

namespace Fuguevit\Repositories\Contracts;

/**
 * Interface RepositoryInterface.
 */
interface RepositoryInterface
{
    /**
     * Retrieve all items.
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function all($columns = ['*']);

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
     * @param array $data
     *
     * @return bool
     */
    public function save(array $data);

    /**
     * Update an item.
     *
     * @param array $attributes
     * @param $id
     *
     * @return mixed
     */
    public function update(array $attributes, $id);

    /**
     * Delete an item.
     *
     * @param $id
     *
     * @return bool
     */
    public function delete($id);

    /**
     * Fetch an item by id.
     *
     * @param $id
     * @param array $columns
     *
     * @return mixed
     */
    public function find($id, $columns = ['*']);

    /**
     * Fetch an item by value of specified field.
     *
     * @param $field
     * @param $value
     * @param array $columns
     *
     * @return mixed
     */
    public function findBy($field, $value, $columns = ['*']);

    /**
     * Find data by multiple fields in one field.
     *
     * @param $field
     * @param array $values
     * @param array $columns
     *
     * @return mixed
     */
    public function findIn($field, array $values, $columns = ['*']);

    /**
     * Find data exclude multiple fields in on field.
     *
     * @param $field
     * @param array $values
     * @param array $columns
     *
     * @return mixed
     */
    public function findNotIn($field, array $values, $columns = ['*']);

    /**
     * Fetch items by where clause.
     *
     * @param $where
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhere($where, $columns = ['*']);

    /**
     * Paginate items.
     *
     * @param $perPage
     * @param array $columns
     *
     * @return mixed
     */
    public function paginate($perPage = 1, $columns = ['*']);

    /**
     * Order collection by a given column.
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
