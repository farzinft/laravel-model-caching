<?php

namespace Fthi\ModelCaching;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;


class ModelCaching
{
    protected $model;
    protected $builder;
    protected $methods = [];
    protected $parameters = [];
    protected $result;

    protected static $cacheExpTime = 40;

    public function __construct(Model $model)
    {
        static::$cacheExpTime = isset($model->cacheExpireTime) ? $model->cacheExpireTime : config('model-caching.cache_expire_time');
        $this->model = $model;
        $this->builder = $model->newQuery();

    }

    public function __call($method, $parameters)
    {

        array_push($this->methods, $method);
        array_push($this->parameters, $this->processParameters($parameters));

        $cacheKey = $this->getCacheKey();

        return CacheManager::getFromCache($cacheKey, static::$cacheExpTime, function ($cacheManager) use ($method, $parameters) {

            $class = new \ReflectionClass($this->model);

            if ($class->hasMethod($method)) {
                $this->result = $this->model->$method(...$parameters);
            } else {
                $this->result = $this->builder->$method(...$parameters);
            }

            if ($this->result instanceof Model || $this->result instanceof Collection) {
                $cacheManager->cache();
                return $this->result;
            }

            $cacheManager->dontCache();

            return $this;
        });

    }

    public function processParameters($params = [])
    {
        return array_map(function ($item) {
            if (($item instanceof \Closure) || (is_array($item) && $item = array_values($item)[0] && $item instanceof \Closure)) {
                $ref = new \ReflectionFunction($item);
                return get_class($ref->getClosureThis()) . $ref->getStartLine() . $ref->getEndLine();
            }
            return $item;
        }, $params);
    }

    private function getCacheKey()
    {
        return md5(sprintf('model_cache_%s_%s',
            serialize($this->methods),
            serialize($this->parameters)
        ));
    }

}