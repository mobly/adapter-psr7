<?php

namespace Mobly\Cache\Interfaces;

/**
 * Interface HasExpirationDateInterface
 * @package Mobly\Cache\Interfaces
 */
interface ConfigurationInterface
{

    /**
     * @return string
     */
    public function getHost();

    /**
     * @param $host
     */
    public function setHost($host);

    /**
     * @return int
     */
    public function getPort();

    /**
     * @param $port
     */
    public function setPort($port);

    /**
     * @return int
     */
    public function getTimeToLive();

    /**
     * @param $timeToLive
     */
    public function setTimeToLive($timeToLive);

    /**
     * @return boolean
     */
    public function getPersistent();

    /**
     * @param $persistent
     */
    public function setPersistent($persistent);
}
