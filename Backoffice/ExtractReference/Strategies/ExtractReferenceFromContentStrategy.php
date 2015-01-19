<?php

namespace PHPOrchestra\Backoffice\ExtractReference\Strategies;

use PHPOrchestra\Backoffice\ExtractReference\ExtractReferenceInterface;
use PHPOrchestra\Media\Model\MediaInterface;
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
            if (strpos($attribute->getValue(), MediaInterface::MEDIA_PREFIX) === 0) {
                $references[substr($attribute->getValue(), strlen(MediaInterface::MEDIA_PREFIX))][] = 'content-' . $statusableElement->getId();
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
