<?php

namespace Mobly\Cache;

use Mobly\Cache\Exception\CacheException;
use Mobly\Cache\Interfaces\ConfigurationInterface;
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
        $configuration = self::getConfigurationByAdapterName($adapterName, $options);
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
     * @param ConfigurationInterface $configuration
     * @return mixed
     */
    private static function getAdapterByName($adapterName, ConfigurationInterface $configuration)
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

    /**
     * @param $adapterName
     * @param array $options
     */
    private function getConfigurationByAdapterName($adapterName, array $options)
    {
        $adapterName = ucfirst($adapterName);
        $configurationClass = sprintf('Mobly\\Cache\\Configuration\\%s\\%sConfiguration', $adapterName, $adapterName);
        if (!class_exists($configurationClass)) {
            throw new CacheException(sprintf('Configuration %s not found.', $adapterName));
        }

        return new $configurationClass($options);
    }

}