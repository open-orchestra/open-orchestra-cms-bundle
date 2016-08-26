<?php

namespace OpenOrchestra\Backoffice\Reference\Strategies;

use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

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
     * @param mixed $entity
     */
    public function addreferencesToEntity($entity)
    {
        if ($this->support($entity)) {
            $keywordIds = $this->extractKeywordsFromNode($entity);

            foreach ($keywordIds as $keywordId) {
                /** @var OpenOrchestra\ModelInterface\Model\KeywordInterface $keyword */
                $keyword = $this->keywordRepository->find($keywordId);
                if ($keyword) {
                    $keyword->addUseInEntity($entity->getId(), NodeInterface::ENTITY_TYPE);
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
     * @param ReadNodeInterface $node
     *
     * @return array
     */
    protected function extractKeywordsFromNode(ReadNodeInterface $node)
    {
        $references = array();

        /** @var BlockInterface $block */
        foreach ($node->getBlocks() as $block) {
            $references = $this->extractKeywordsFromElement($block->getAttributes(), $references);
        }

        return $references;
    }
}
