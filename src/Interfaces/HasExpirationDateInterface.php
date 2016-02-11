<?php

namespace Mobly\Cache\Interfaces;

/**
 * Interface HasExpirationDateInterface
 * @package Mobly\Cache\Interfaces
 */
interface HasExpirationDateInterface
{
    /**
     * The date and time when the object expires.
     *
     * @return \DateTime|null
     */
    public function getExpirationDate();
}
