<?php

namespace OpenOrchestra\Backoffice\Reference\Strategies;

use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Event\NodeEvent;

/**
 * Class KeywordInNodeReferenceStrategy
 */
class KeywordInNodeReferenceStrategy extends AbstractKeywordReferenceStrategy implements ReferenceStrategyInterface
{
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
    public function addreferencesToEntity($event)
    {
        $node = $event->getNode();
        if ($this->support($node)) {
            $keywordIds = $this->extractKeywordsFromNode($event);

            foreach ($keywordIds as $keywordId) {
                /** @var \OpenOrchestra\ModelInterface\Model\KeywordInterface $keyword */
                $keyword = $this->keywordRepository->find($keywordId);
                if ($keyword) {
                    $keyword->addUseInEntity($node->getId(), NodeInterface::ENTITY_TYPE);
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

            $keywordsUsedInNode = $this->keywordRepository->findByUsedInEntity($nodeId, NodeInterface::ENTITY_TYPE);

            foreach ($keywordsUsedInNode as $keyword) {
                $keyword->removeUseInEntity($nodeId, NodeInterface::ENTITY_TYPE);
            }
        }
    }

    /**
     * @param NodeEvent $event
     *
     * @return array
     */
    protected function extractKeywordsFromNode(NodeEvent $event)
    {
        $references = array();

        $blocks = ($event->getBlock() != null) ? array($event->getBlock()) : $event->getNode()->getBlocks();

        /** @var \OpenOrchestra\ModelInterface\Model\BlockInterface $block */
        foreach ($blocks as $block) {
            $references = $this->extractKeywordsFromElement($block->getAttributes(), $references);
        }

        return $references;
    }
}
