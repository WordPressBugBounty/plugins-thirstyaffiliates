<?php

declare(strict_types=1);

namespace ThirstyAffiliates\GroundLevel\Container;

use ThirstyAffiliates\GroundLevel\Container\Concerns\HasContainer;
use ThirstyAffiliates\GroundLevel\Container\Contracts\ContainerAwareness;

class Service implements ContainerAwareness
{
    use HasContainer;

    /**
     * Service constructor.
     *
     * @param Container $container The container instance.
     */
    public function __construct(Container $container)
    {
        $this->setContainer($container);
    }
}
