<?php

namespace OpenOrchestra\Backoffice\Reference\Strategies;

use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

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
     * @param mixed $entity
     * @param mixed $subEntity
     */
    public function addReferencesToEntity($entity, $subEntity)
    {
        if ($this->support($entity)) {
            $contentIds = $this->extractContentsFromNode($entity, $subEntity);

            foreach ($contentIds as $contentId) {
                /** @var OpenOrchestra\ModelInterface\Model\ContentInterface $content */
                $contents = $this->contentRepository->findByContentId($contentId);

                if (is_array($contents)) {
                    foreach ($contents as $content) {
                        $content->addUseInEntity($entity->getId(), NodeInterface::ENTITY_TYPE);
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
     * @param ReadNodeInterface $node
     * @param mixed             $subEntity
     *
     * @return array
     */
    protected function extractContentsFromNode(ReadNodeInterface $node, $subEntity)
    {
        $references = array();

        $blocks = !is_null($subEntity) ? $node->getBlocks() : array($subEntity);

        /** @var BlockInterface $block */
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
