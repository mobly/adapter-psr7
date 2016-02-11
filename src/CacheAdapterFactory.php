<?php

namespace Mobly\Cache;

use Mobly\Cache\Exception\CacheException;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class CacheAdapterFactory
 * @package Mobly\Cache
 */
class CacheAdapterFactory
{

    /**
     * @param $adapterName
     * @param array $options
     * @return CacheItemPoolInterface
     */
    public static function create($adapterName, array $options)
    {
        $configuration = new CacheAdapterConfiguration($options);
        if ($adapterName instanceof CacheItemPoolInterface) {
            // $adapterName is already an adapter object
            $adapter = $adapterName;
            $adapter->setConfiguration($configuration);

            return $adapter;
        }

        $adapter = self::getAdapterByName($adapterName, $configuration);

        return $adapter;
    }

    /**
     * @param $adapterName
     * @param CacheAdapterConfiguration $configuration
     * @return mixed
     */
    private static function getAdapterByName($adapterName, CacheAdapterConfiguration $configuration)
    {
        $adapterName = ucfirst($adapterName);
        $adapterClass = sprintf('Mobly\\Cache\\Adapter\\%s\\%sAdapter', $adapterName, $adapterName);
        if (!class_exists($adapterClass)) {
            throw new CacheException(sprintf('Adapter %s not found.', $adapterName));
        }

        $adapter = $adapterClass::getInstance($configuration);
        if(!$adapter instanceof CacheItemPoolInterface) {
            throw new CacheException(sprintf('Adapter %s not implement CacheItemPoolInterface.', $adapterName));
        }

        return $adapter;
    }

}