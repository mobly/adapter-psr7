<?php

namespace Mobly\Cache\Exception;

use Psr\Cache\CacheException as CacheExceptionInterface;

/**
 * Class CacheException
 * @package Mobly\Cache\Exception
 */
class CacheException extends \InvalidArgumentException implements CacheExceptionInterface
{
}