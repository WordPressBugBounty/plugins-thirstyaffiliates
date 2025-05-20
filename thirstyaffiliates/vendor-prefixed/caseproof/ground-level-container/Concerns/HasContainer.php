<?php

declare(strict_types=1);

namespace ThirstyAffiliates\GroundLevel\Container\Concerns;

use ThirstyAffiliates\GroundLevel\Container\Container;
use ThirstyAffiliates\GroundLevel\Container\Contracts\ContainerAwareness;

trait HasContainer
{
    /**
     * The container instance.
     *
     * @var Container
     */
    protected Container $container;

    /**
     * Retrieves a container.
     *
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Sets a container.
     *
     * @param  Container $container The container.
     * @return ContainerAwareness
     */
    public function setContainer(Container $container): ContainerAwareness
    {
        $this->container = $container;
        return $this;
    }
}
