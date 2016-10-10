<?php

namespace Fuguevit\Repositories\Cache;

use BadMethodCallException;
use Fuguevit\Repositories\Contracts\CacheInterface;
use Illuminate\Container\Container as App;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

abstract class CacheDecorator implements CacheInterface
{
    public $repository;
    protected $ttl;
    protected $prefix_key;
    protected $enabled = true;
    protected $excludes = false;
    protected $tag_cleaners = [];
    protected $tags = false;
    protected $debug = false;
    protected $app;
    protected $collection;

    /**
     * You need to implement this per sub-class.
     *
     * @return string Name of the repository class. Used for instiating the repository
     */
    abstract public function repository();

    /**
     * CacheDecorator constructor.
     *
     * @param App        $app
     * @param Collection $collection
     * @param bool       $repository
     */
    public function __construct(App $app, Collection $collection, $repository = false)
    {
        $this->app = $app;
        $this->collection = $collection;
        $this->initExcludes();
        $this->initRepository($repository);
        $this->getConfig();
    }

    protected function initExcludes()
    {
        $defaults = ['repository', 'setTtl', 'setEnabled', 'getConfig', 'initRepository',
            'doesMethodClearTag', 'clearCacheTag', 'getCache', 'putCache',
            'isMethodCacheable', 'generateCacheKey', 'log', ];
        $this->excludes = array_merge($defaults, $this->excludes);
    }

    public function setTtl($minutes)
    {
        $this->ttl = $minutes;
    }

    public function setEnabled($bool)
    {
        $this->enabled = $bool;
    }

    protected function getConfig()
    {
        $this->ttl = Config::get('repository_cache.ttl');
        $this->enabled = Config::get('repository_cache.enabled');
        if (!Config::get('repository_cache.use_tags')) {
            $this->tags = false;
            $this->tag_cleaners = false;
        }
        $this->debug = Config::get('app.debug');
    }

    public function initRepository($repository)
    {
        if (!$repository) {
            $class = $this->repository();
            $repository = new $class($this->app, $this->collection);
        }
        $this->repository = $repository;
    }

    public function __call($method, $arguments)
    {
        if ($this->isMethodCacheable($method)) {
            $key = $this->generateCacheKey($method, $arguments);
            $res = $this->getCache($key);
            if (!$res) {
                $res = $this->callMethod($method, $arguments);
                $this->putCache($key, $res);
            }
        } else {
            $res = $this->callMethod($method, $arguments);
        }
        if ($this->doesMethodClearTag($method)) {
            $this->clearCacheTag();
        }

        return $res;
    }

    protected function doesMethodClearTag($method)
    {
        if ($this->tag_cleaners &&
            in_array($method, $this->tag_cleaners)) {
            return true;
        }
    }

    protected function clearCacheTag()
    {
        if ($this->tags) {
            return Cache::tags($this->tags)->flush();
        }
    }

    protected function getCache($key)
    {
        if ($this->ttl === false) {
            return false;
        }

        if ($this->tags) {
            return Cache::tags($this->tags)->get($key);
        }

        return Cache::get($key);
    }

    protected function putCache($key, $res)
    {
        if ($this->ttl === false) { // don't save if ttl is false
            return false;
        }
        if ($this->tags) {
            return Cache::tags($this->tags)->put($key, $res, $this->ttl);
        }

        return Cache::put($key, $res, $this->ttl);
    }

    protected function callMethod($method, $arguments)
    {
        if (method_exists($this->repository, $method)) {
            return call_user_func_array([$this->repository, $method], $arguments);
        }
        throw new BadMethodCallException("Method '{$method}' does not exist in the repository");
    }

    protected function isMethodCacheable($method)
    {
        if ($this->excludes && in_array($method, $this->excludes)) {
            return false;
        }

        return true;
    }

    protected function generateCacheKey($method, $arguments)
    {
        $temp_params = array_dot($arguments);
        $params = '';
        foreach ($temp_params as $k => $v) {
            $params .= ".{$k}={$v}";
        }
        $key = "{$this->prefix_key}.{$method}{$params}";

        return $key;
    }
}
