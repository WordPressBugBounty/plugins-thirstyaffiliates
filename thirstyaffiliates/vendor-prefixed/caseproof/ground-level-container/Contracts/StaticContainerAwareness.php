<?php

declare(strict_types=1);

namespace ThirstyAffiliates\GroundLevel\Container\Contracts;

use ThirstyAffiliates\GroundLevel\Container\Container;

interface StaticContainerAwareness
{
    /**
     * Retrieves a container.
     *
     * @return Container
     */
    public static function getContainer(): Container;

    /**
     * Sets a container.
     *
     * @param Container $container The container.
     */
    public static function setContainer(Container $container): void;
}
