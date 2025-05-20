<?php

declare(strict_types=1);

namespace ThirstyAffiliates\GroundLevel\Container\Contracts;

use ThirstyAffiliates\GroundLevel\Container\Container;

interface LoadableDependency
{
    /**
     * Loads the dependency.
     *
     * This method is called automatically when the dependency is instantiated.
     *
     * @param Container $container The container.
     */
    public function load(Container $container): void;
}
