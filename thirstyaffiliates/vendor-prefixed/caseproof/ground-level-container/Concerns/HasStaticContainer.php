<?php

declare(strict_types=1);

namespace ThirstyAffiliates\GroundLevel\Container\Concerns;

use ThirstyAffiliates\GroundLevel\Container\Container;

trait HasStaticContainer
{
    /**
     * The static container instance.
     *
     * @var Container
     */
    protected static Container $container;

    /**
     * Retrieves a container.
     *
     * @return Container
     */
    public static function getContainer(): Container
    {
        return static::$container;
    }

    /**
     * Sets a container.
     *
     * @param Container $container The container.
     */
    public static function setContainer(Container $container): void
    {
        static::$container = $container;
    }
}
