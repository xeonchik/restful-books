<?php

namespace AppBase\Cache;

use Doctrine\Common\Cache\CacheProvider;

/**
 * Class CacheWrapper
 * @package AppBase\Cache
 */
class CacheWrapper
{
    /**
     * @var CacheProvider
     */
    protected $cacheProvider;

    /**
     * CacheWrapper constructor.
     * @param CacheProvider $cacheProvider
     */
    public function __construct(CacheProvider $cacheProvider)
    {
        $this->cacheProvider = $cacheProvider;
    }

    /**
     * Cache wrapper function
     *
     * @param $dataLogic
     * @param $options
     * @param mixed ...$params
     * @return mixed
     */
    public function wrap($dataLogic, $options, ...$params)
    {
        $key = isset($options['key']) ? $options['key'] : null;
        $ttl = isset($options['ttl']) ? $options['ttl'] : 3600 * 24;
        $hashParams = isset($options['hash_params']) ? (bool)$options['hash_params'] : false;

        if ($key == null) {
            $trace = debug_backtrace();
            $callerFunc = isset($trace[1]['function']) ? $trace[1]['function'] : '';
            $callerClass = isset($trace[1]['class']) ? $trace[1]['class'] : '';
            $key = $callerClass . '_' . $callerFunc;
        }

        $paramsKeyPart = '';
        foreach ($params as $value) {
            $paramsKeyPart .= '_' . (string)$value;
        }

        if ($hashParams) {
            $paramsKeyPart = sha1($key);
        }

        $key .= $paramsKeyPart;
        $data = $this->cacheProvider->fetch($key);

        if ($data !== false) {
            return json_decode($data, true);
        }

        $data = $dataLogic(...$params);
        $this->cacheProvider->save($key, json_encode($data), $ttl);
        return $data;
    }
}
