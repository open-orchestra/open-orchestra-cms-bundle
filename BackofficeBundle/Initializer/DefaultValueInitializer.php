<?php

namespace OpenOrchestra\BackofficeBundle\Initializer;

use Doctrine\Common\Collections\Collection;

/**
 * Class TranslatedValueDefaultValueInitializer
 */
interface DefaultValueInitializer
{
    /**
     * @param Collection $properties
     */
    public function generate(Collection $properties);
}