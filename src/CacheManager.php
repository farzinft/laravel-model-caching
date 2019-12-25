<?php

namespace Fthi\ModelCaching;

use Illuminate\Support\Facades\Redis;

class CacheManager
{

    public static $dontCache = false;

    protected static $cachePrefix;


    public function __construct()
    {
        static::$cachePrefix = config('model-caching.cache_prefix');
    }

    public static function getFromCache($key, $sec, callable $callback)
    {

        $key = static::getCacheKey($key);

        if ($value = Redis::get($key)) {
            return static::unserialize($value);
        }

        $res = call_user_func($callback, new static());

        if(static::$dontCache == false) {
            Redis::setex($key, $sec, static::serialize($res));
        }

        return $res;
    }


    public function getTTL($key)
    {
        return Redis::ttl(static::getCacheKey($key));
    }


    protected static function serialize($value)
    {
        return is_numeric($value) ? $value : serialize($value);
    }


    protected static function unserialize($value)
    {
        return is_numeric($value) ? $value : unserialize($value);
    }

    public function dontCache()
    {
       static::$dontCache = true;
    }

    public function cache()
    {
        static::$dontCache = false;
    }

    private static function getCacheKey($key)
    {
        return static::$cachePrefix . $key;
    }

}