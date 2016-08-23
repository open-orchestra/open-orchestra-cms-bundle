<?php

namespace OpenOrchestra\Backoffice\Reference\Strategie;

use OpenOrchestra\ModelInterface\Model\StatusableInterface;

/**
 * Interface ReferenceStrategy
 */
Interface ReferenceStrategyInterface
{
    /**
     * @param StatusableInterface $statusableElement
     *
     * @return boolean
     */
    public function support(StatusableInterface $statusableElement);

    /**
     * @param StatusableInterface $statusableElement
     */
    public function addreferencesToEntity(StatusableInterface $statusableElement);

    /**
     * @param StatusableInterface $statusableElement
     */
    public function removeReferencesToEntity(StatusableInterface $statusableElement);
}