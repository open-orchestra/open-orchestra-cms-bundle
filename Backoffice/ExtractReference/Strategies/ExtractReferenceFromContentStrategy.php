<?php

namespace PHPOrchestra\Backoffice\ExtractReference\Strategies;

use PHPOrchestra\Backoffice\ExtractReference\ExtractReferenceInterface;
use PHPOrchestra\ModelInterface\Model\ContentAttributeInterface;
use PHPOrchestra\ModelInterface\Model\ContentInterface;
use PHPOrchestra\ModelInterface\Model\StatusableInterface;

/**
 * Class ExtractReferenceFromContentStrategy
 */
class ExtractReferenceFromContentStrategy implements ExtractReferenceInterface
{
    /**
     * @param StatusableInterface $statusableElement
     *
     * @return bool
     */
    public function support(StatusableInterface $statusableElement)
    {
        return $statusableElement instanceof ContentInterface;
    }

    /**
     * @param StatusableInterface|ContentInterface $statusableElement
     *
     * @return array
     */
    public function extractReference(StatusableInterface $statusableElement)
    {
        $references = array();

        /** @var ContentAttributeInterface $attribute */
        foreach ($statusableElement->getAttributes() as $attribute) {
            if (strpos($attribute->getValue(), 'media-') === 0) {
                $references[substr($attribute->getValue(), 6)][] = 'content-' . $statusableElement->getId();
            }
        }

        return $references;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content';
    }
}
