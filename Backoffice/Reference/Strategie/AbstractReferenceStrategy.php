<?php

namespace OpenOrchestra\Backoffice\Reference\Strategie;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class AbstractReferenceStrategy
 */
abstract class AbstractReferenceStrategy
{
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }
}