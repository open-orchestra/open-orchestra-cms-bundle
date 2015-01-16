<?php

namespace PHPOrchestra\Backoffice\ExtractReference\Strategies;

use PHPOrchestra\Backoffice\ExtractReference\ExtractReferenceInterface;
use PHPOrchestra\ModelInterface\Model\NodeInterface;
use PHPOrchestra\ModelInterface\Model\StatusableInterface;

/**
 * Class ExtractReferenceFromNodeStrategy
 */
class ExtractReferenceFromNodeStrategy implements ExtractReferenceInterface
{
    /**
     * @param StatusableInterface $statusableElement
     *
     * @return bool
     */
    public function support(StatusableInterface $statusableElement)
    {
        return $statusableElement instanceof NodeInterface;
    }

    /**
     * @param StatusableInterface|NodeInterface $statusableElement
     *
     * @return array
     */
    public function extractReference(StatusableInterface $statusableElement)
    {
    }
}
