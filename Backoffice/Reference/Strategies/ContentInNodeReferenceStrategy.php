<?php

namespace OpenOrchestra\Backoffice\Reference\Strategies;

use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Event\NodeEvent;

/**
 * Class ContentInNodeReferenceStrategy
 */
class ContentInNodeReferenceStrategy implements ReferenceStrategyInterface
{
    protected $contentRepository;

    /**
     * @param ContentRepositoryInterface $contentRepository
     */
    public function __construct(ContentRepositoryInterface $contentRepository)
    {
        $this->contentRepository = $contentRepository;
    }

    /**
     * @param mixed $entity
     *
     * @return boolean
     */
    public function support($entity)
    {
        return ($entity instanceof ReadNodeInterface);
    }

    /**
     * @param mixed $event
     */
    public function addReferencesToEntity($event)
    {
        $node = $event->getNode();
        if ($this->support($node)) {
            $contentIds = $this->extractContentsFromNode($event);

            foreach ($contentIds as $contentId) {
                /** @var \OpenOrchestra\ModelInterface\Model\ContentInterface $content */
                $contents = $this->contentRepository->findByContentId($contentId);

                if (is_array($contents)) {
                    foreach ($contents as $content) {
                        $content->addUseInEntity($node->getId(), NodeInterface::ENTITY_TYPE);
                    }
                }
            }
        }
    }

    /**
     * @param mixed $entity
     */
    public function removeReferencesToEntity($entity)
    {
        if ($this->support($entity)) {
            $nodeId = $entity->getId();

            $contentsUsedInNode = $this->contentRepository->findByUsedInEntity($nodeId, NodeInterface::ENTITY_TYPE);

            foreach ($contentsUsedInNode as $content) {
                $content->removeUseInEntity($nodeId, NodeInterface::ENTITY_TYPE);
            }
        }
    }

    /**
     * @param NodeEvent $event
     *
     * @return array
     */
    protected function extractContentsFromNode(NodeEvent $event)
    {
        $references = array();

        $blocks = ($event->getBlock() != null) ? array($event->getBlock()) : $event->getNode()->getBlocks();

        /** @var \OpenOrchestra\ModelInterface\Model\BlockInterface $block */
        foreach ($blocks as $block) {
            $references = $this->extractContentsFromElement($block->getAttributes(), $references);
        }

        return $references;
    }

    /**
     * Recursively extract content ids from elements (bloc, attribute, collection attribute, etc ...)
     *
     * @param mixed $element
     * @param array $references
     *
     * @return array
     */
    protected function extractContentsFromElement($element, array $references = array())
    {
        if ($this->isContentSearchAttribute($element)) {
            $references[] = $element['contentId'];
        } elseif (is_array($element)) {
            foreach ($element as $item) {
                $references = $this->extractContentsFromElement($item, $references);
            }
        }

        return $references;
    }

    /**
     * Check if $attributeValue matches with a content search attribute
     *
     * @param mixed $attributeValue
     *
     * @return boolean
     */
    protected function isContentSearchAttribute($attributeValue)
    {
        return is_array($attributeValue)
        && array_key_exists('contentType', $attributeValue)
        && array_key_exists('choiceType', $attributeValue)
        && array_key_exists('keywords', $attributeValue)
        && isset($attributeValue['contentId']);
    }
}
