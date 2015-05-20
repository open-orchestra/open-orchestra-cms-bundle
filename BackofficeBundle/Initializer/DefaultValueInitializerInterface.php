<?php

namespace OpenOrchestra\BackofficeBundle\Initializer;

use Doctrine\Common\Collections\Collection;

/**
 * Class DefaultValueInitializerInterface
 */
interface DefaultValueInitializerInterface
{
    /**
     * @param Collection $properties
     */
    public function generate(Collection $properties);
}
