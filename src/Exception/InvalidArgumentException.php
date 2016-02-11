<?php

namespace Mobly\Cache\Exception;

use Psr\Cache\InvalidArgumentException as InvalidArgumentExceptionInterface;

/**
 * Class InvalidArgumentException
 * @package Mobly\Cache\Exception
 */
class InvalidArgumentException extends \InvalidArgumentException implements InvalidArgumentExceptionInterface
{
}