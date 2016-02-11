<?php

namespace Mobly\Cache;

use Mobly\Cache\Exception\InvalidArgumentException;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Mobly\Cache\Interfaces\HasExpirationDateInterface;

/**
 * Class AbstractCacheAdapter
 * @package Mobly\Cache
 */
abstract class AbstractCacheAdapter implements CacheItemPoolInterface
{
    /**
     * @type CacheItemInterface[] deferred
     */
    protected $deferred = [];

    /**
     * Make sure to commit before we destruct.
     */
    public function __destruct()
    {
        $this->commit();
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($key)
    {
        $this->validateKey($key);

        return $this->fetchObjectFromCache($key);
    }

    /**
     * @param $key
     * @return mixed
     */
    abstract protected function fetchObjectFromCache($key);

    /**
     * @param array $keys
     * @return mixed
     */
    abstract protected function fetchMultiObjectsFromCache(array $keys);

    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = [])
    {
        foreach ($keys as $key) {
            $this->validateKey($key);
        }

        return $this->fetchMultiObjectsFromCache($keys);
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($key)
    {
        return $this->getItem($key)->isHit();
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        // Clear the deferred items
        $this->deferred = [];

        return $this->clearAllObjectsFromCache();
    }

    /**
     * Clear all objects from cache.
     *
     * @return bool false if error
     */
    abstract protected function clearAllObjectsFromCache();

    /**
     * {@inheritdoc}
     */
    public function deleteItem($key)
    {
        return $this->deleteItems([$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys)
    {
        $deleted = true;
        foreach ($keys as $key) {
            $this->validateKey($key);

            // Delete form deferred
            unset($this->deferred[$key]);

            if (!$this->clearOneObjectFromCache($key)) {
                $deleted = false;
            }
        }

        return $deleted;
    }

    /**
     * Remove one object from cache.
     *
     * @param string $key
     *
     * @return bool
     */
    abstract protected function clearOneObjectFromCache($key);

    /**
     * {@inheritdoc}
     */
    public function save(CacheItemInterface $item)
    {
        $key = $item->getKey();

        $timeToLive = null;
        if ($item instanceof HasExpirationDateInterface) {
            if (null !== $expirationDate = $item->getExpirationDate()) {
                $timeToLive = $expirationDate->getTimestamp() - time();
            }
        }

        return $this->storeItemInCache($key, $item, $timeToLive);
    }

    /**
     * @param string             $key
     * @param CacheItemInterface $item
     * @param int|null           $ttl  seconds from now
     *
     * @return bool true if saved
     */
    abstract protected function storeItemInCache($key, CacheItemInterface $item, $ttl);

    /**
     * {@inheritdoc}
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $key = $item->getKey();
        $this->deferred[$key] = $item;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        $saved = true;
        foreach ($this->deferred as $item) {
            if (!$this->save($item)) {
                $saved = false;
            }
        }
        $this->deferred = [];

        return $saved;
    }

    /**
     * @param string $key
     *
     * @throws InvalidArgumentException
     */
    protected function validateKey($key)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException(sprintf(
                'Cache key must be string, "%s" given', gettype($key)
            ));
        }

        if (preg_match('|[\{\}\(\)/\\\@\:]|', $key)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid key: "%s". The key contains one or more characters reserved for future extension: {}()/\@:',
                $key
            ));
        }
    }

}