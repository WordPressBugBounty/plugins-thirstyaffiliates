<?php

declare(strict_types=1);

namespace ThirstyAffiliates\GroundLevel\Container\Contracts;

use ThirstyAffiliates\GroundLevel\Container\Container;

interface ConfiguresParameters
{
    /**
     * Configures the dependency's parameters.
     *
     * If a default parameter already exists on the container, it will not be overwritten,
     * otherwise it will be added to the container using the default value.
     *
     * @param \ThirstyAffiliates\GroundLevel\Container\Container $container The container.
     */
    public function configureParameters(Container $container): void;

    /**
     * Returns a key=>value list of default parameters.
     *
     * @return array
     */
    public function getDefaultParameters(): array;
}
