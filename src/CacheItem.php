<?php

namespace Mobly\Cache;

use Psr\Cache\CacheItemInterface;
use Mobly\Cache\Interfaces\HasExpirationDateInterface;

/**
 * Class CacheItem
 * @package Mobly\Cache
 */
class CacheItem implements CacheItemInterface, HasExpirationDateInterface
{

    /**
     * @type string
     */
    private $key;

    /**
     * @type mixed
     */
    private $value;

    /**
     * @type \DateTimeInterface|null
     */
    private $expirationDate = null;

    /**
     * @type bool
     */
    private $hasValue = false;

    /**
     * @param $key
     * @param null $value
     */
    public function __construct($key, $value = null)
    {
        $this->key = $key;
        if ($value) {
            $this->set($value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function set($value)
    {
        $this->value = $value;
        $this->hasValue = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        if (!$this->isHit()) {
            return false;
        }

        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function isHit()
    {
        if (!$this->hasValue) {
            return false;
        }

        if ($this->expirationDate !== null) {
            return $this->expirationDate > new \DateTime();
        }

        return true;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAt($expiration)
    {
        $this->expirationDate = $expiration;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAfter($time)
    {
        if ($time === null) {
            $this->expirationDate = null;
        }

        if ($time instanceof \DateInterval) {
            $this->expirationDate = new \DateTime();
            $this->expirationDate->add($time);
        }

        if (is_int($time)) {
            $this->expirationDate = new \DateTime(sprintf('+%sseconds', $time));
        }

        return $this;
    }
}
