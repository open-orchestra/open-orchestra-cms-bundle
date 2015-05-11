<?php

namespace OpenOrchestra\MediaAdminBundle\ExtractReference\Strategies;

use OpenOrchestra\MediaAdminBundle\ExtractReference\ExtractReferenceInterface;
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
            $value = $attribute->getValue();
            if (is_string($value) && strpos($value, MediaInterface::MEDIA_PREFIX) === 0) {
                $references[substr($value, strlen(MediaInterface::MEDIA_PREFIX))][] = 'content-' . $statusableElement->getId();
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
