<?php

namespace Mobly\Cache;

use Mobly\Cache\Exception\CacheException;

/**
 * Class CacheAdapterConfiguration
 * @package Mobly\Cache
 */
class CacheAdapterConfiguration
{

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var int
     */
    protected $ttl = 0;

    /**
     * @var bool
     */
    protected $persistent;

    protected $required = [
        'host',
        'port'
    ];

    /**
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        $this->setup($configuration);
    }

    /**
     * @param array $configuration
     */
    private function setup(array $configuration)
    {
        $properties = get_object_vars($this);
        $configurationKeys = array_keys($configuration);
        foreach ($properties as $property => $value) {
            if (in_array($property, $configurationKeys)) {
                $this->$property =  $configuration[$property];
            }
        }

        $this->validate();
    }

    /**
     * @return bool
     */
    private function validate() {
        $errors = [];
        foreach ($this->required as $required) {
            if (empty($this->$required)) {
                $errors[] = sprintf('%s is required', $required);
            }
        }

        if (count($errors)) {
            throw new CacheException('Invalid configuration. ' . implode(', ', $errors));
        }

        return true;
    }


    /**
     * @param $host
     */
    public function setHost($host)
    {
        $this->host = (string) $host;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return (string) $this->host;
    }

    /**
     * @param $port
     */
    public function setPort($port)
    {
        $this->port = (int) $port;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return (int) $this->port;
    }

    /**
     * @param $ttl
     */
    public function setTimeToLive($ttl)
    {
        $this->ttl = (int) $ttl;
    }

    /**
     * @return int
     */
    public function getTimeToLive()
    {
        return (int) $this->ttl;
    }

    /**
     * @param $persistent
     */
    public function setPersistent($persistent)
    {
        $this->persistent = (bool) $persistent;
    }

    /**
     * @return bool
     */
    public function getPersistent()
    {
        return (bool) $this->persistent;
    }
}