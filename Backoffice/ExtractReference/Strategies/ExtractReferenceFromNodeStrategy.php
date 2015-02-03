<?php

namespace PHPOrchestra\Backoffice\ExtractReference\Strategies;

use PHPOrchestra\Backoffice\ExtractReference\ExtractReferenceInterface;
use PHPOrchestra\Media\Model\MediaInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
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
        $references = array();

        /** @var BlockInterface $block */
        foreach ($statusableElement->getBlocks() as $key => $block) {
            $references = array_merge(
                $references,
                $this->extractMedia($block->getAttributes(), $key, $statusableElement->getId() , $references)
            );
        }

        return $references;
    }

    /**
     * Recursively extract media references from elements (bloc, attribute, collection attribute, etc ...)
     * 
     * @param array  $element
     * @param string $blockIndex
     * @param string $statusableElementId
     * @param array  $references
     */
    protected function extractMedia($element, $blockIndex, $statusableElementId, $references = array())
    {
        if (is_array($element)) {
            foreach ($element as $item) {
                $references = array_merge(
                    $references,
                    $this->extractMedia($item, $blockIndex, $statusableElementId , $references)
                );
            }
        } elseif (is_string($element) && strpos($element, MediaInterface::MEDIA_PREFIX) === 0) {
            $references[substr($element, strlen(MediaInterface::MEDIA_PREFIX))][] = 'node-' . $statusableElementId . '-' . $blockIndex;
        }

        return $references;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'node';
    }
}
