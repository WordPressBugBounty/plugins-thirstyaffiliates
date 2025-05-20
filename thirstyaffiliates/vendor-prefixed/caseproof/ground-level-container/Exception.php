<?php

declare(strict_types=1);

namespace ThirstyAffiliates\GroundLevel\Container;

use ThirstyAffiliates\Psr\Container\ContainerExceptionInterface;
use Exception as BaseException;

class Exception extends BaseException implements ContainerExceptionInterface
{
}
