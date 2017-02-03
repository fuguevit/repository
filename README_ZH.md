# Laravel5 Repository (中文)

Laravel5 Repository 是对Laravel的仓库模式的实现。

## 安装

在本地工程目录下面，输入下列命令行：

```php
composer require "fuguevit/repositories:^1.0.0"
```

或者，在composer.json文件内插入下行代码：

```php
"fuguevit/repositories": "^1.0.0"
```

保存后，执行 `composer update`

## 概览

首先，在config/app.php文件下的providers数组中添加如下一行：

```php
Fuguevit\Repositories\Providers\RepositoryServiceProvider::class
```

其次，用artisan执行一下命令，将repository.php文件拷贝至项目config目录：

```php
php artisan vendor:publish --provider="Fuguevit\Repositories\Providers\RepositoryServiceProvider" --tag=config
```

配置文件结构如下，请依据自身项目配置做适当调整，例如项目命名空间和目录结构。

```php
  'repository_namespace' => 'App\Repositories',
  'repository_path' 	 => 'app'.DIRECTORY_SEPARATOR.'Repositories',
  'criteria_namespace'   => 'App\Repositories\Criteria',
  'criteria_path'        => 'app'.DIRECTORY_SEPARATOR.'Repositories'.DIRECTORY_SEPARATOR.'Criteria',
  'model_namespace' 	 => 'App',
  'cache_enabled'   	 => env('REPOSITORY_CACHE', true),
  'cache_ttl'       	 => env('REPOSITORY_CACHE_TTL', 30),
  'cache_use_tags'       => env('REPOSITORY_CACHE_TAGS', false),
```

接下来可以创建第一个Repository Class， 假设原项目中有一个叫做Question的Model类，针对其我们可以执行一下方法：

```php
php artisan make:repository QuestionsRepository
```

在配置文件定义的Repository目录下，可以发现QuestionsRepository.php文件已生成。

```php
<?php

namespace App\Repositories;

use Fuguevit\Repositories\Eloquent\Repository;

/**
 * Class QuestionsRepository
 * @package App\Repositories
 */
class QuestionsRepository extends Repository
{
    /**
     * @return string
     */
    public function model()
    {
        return 'App\Question';
    }
}
```

## 使用

正常使用Eloquent Model的情况下，使用方式如下：

```php
#1 根据主键查找
$question = Question::find($id);

#2 根据属性 'category' 的值查找
$questions = Question::where('category', 'others')->get();
```

当使用 Repository 时：

```php
#0 初始化 QuestionsRepository 对象 $question（可以交给Laravel的IoC实现）

#1 根据主键查找
$result = $question->find($id);

#2 根据属性 'category' 的值查找
$results = $question->findBy('category', 'others');
```

下一章节描述Repository的所有方法。

## 方法

Repository实现了下面接口定义的所有方法:

**Fuguevit\Repositories\Contracts\RepositoryInterface**

```php
    public function all($columns = ['*']);
    public function lists($value, $key = null);
    public function create(array $attributes);
    public function save(array $attributes);
    public function update(array $attributes, $id);
    public function delete($id);
    public function find($id, $columns = ['*']);
    public function findBy($field, $value, $columns = ['*']);
    public function findAllBy($field, $value, $columns = ['*']);
    public function findAllExcept($field, $value, $columns = ['*']);
    public function findIn($field, array $values, $columns = ['*']);
    public function findNotIn($field, array $values, $columns = ['*']);
    public function findWhere($where, $columns = ['*']);
    public function paginate($perPage = 1, $columns = ['*']);
    public function orderBy($field, $direction = 'asc');
    public function with(array $relations);
```

**Fuguevit\Repositories\Contracts\CriteriaInterface**

```
    public function apply($model, Repository $repository);
```

**例子**

- Get all entities

```php
    $this->question->all();
```

- Create a new entity

```php
    $this->question->create($fillable);
```

- Update an entity

```php
    $this->question->update($attributes, $id);
```

- Remove an entity

```php
    $this->question->delete($id);
```

- Find an entity default by id

```php
    $this->question->find($id);
```

- Find an entity by field

```php
    $this->question->findBy($field, $value);
```

- Find all entities by field

```php
    $this->question->findAllBy($field, $value;
```

- Find all entities in a range

```php
    $this->question->findIn($field, $values);
```

- Find all entities by complicated multiple fields:

```php
    $this->question->findWhere([
        'user_id' => $userId,
        ['created_at', '>', \Carbon\Carbon::yesterday()],
        ['status', 'in', array('active', 'banned')]
    ]);
```

## Criteria

Criteria 被设计为适合在某一类特定或者复杂的查询条件中使用. 使用者定义的Criteria对象**必须**继承` Fuguevit\Repositories\Criteria\Criteria` 抽象类.

示例:

```php
<?php 

namespace App\Repositories\Criteria\Questions;

use Fuguevit\Repositories\Criteria\Criteria;
use Fuguevit\Repositories\Contracts\RepositoryInterface as Repository;

class MadeByVipUser extends Criteria {

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        $model = $model->whereHas('user', function ($query) {
            $query->where('role', 'vip');
        });
        return $model;
    }
}
```

## Cache

## 感谢
----
本项目是在bosnadev的[Laravel Repository](https://github.com/bosnadev/repository)的启发编写的。
