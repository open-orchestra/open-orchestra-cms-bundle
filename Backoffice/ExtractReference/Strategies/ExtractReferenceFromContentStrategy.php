<?php

namespace OpenOrchestra\Backoffice\ExtractReference\Strategies;

use OpenOrchestra\Backoffice\ExtractReference\ExtractReferenceInterface;
use OpenOrchestra\Media\Model\MediaInterface;
use OpenOrchestra\ModelInterface\Model\ContentAttributeInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;

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
