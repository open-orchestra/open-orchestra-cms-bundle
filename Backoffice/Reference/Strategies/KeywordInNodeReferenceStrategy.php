<?php

namespace OpenOrchestra\Backoffice\Reference\Strategies;

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
        return ($entity instanceof NodeInterface);
    }

    /**
     * @param mixed $entity
     */
    public function addReferencesToEntity($entity)
    {
        if ($this->support($entity)) {
            $keywordIds = $this->extractKeywordsFromKeywordableEntity($entity);

            foreach ($keywordIds as $keywordId) {
                /** @var \OpenOrchestra\ModelInterface\Model\KeywordInterface $keyword */
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
            $contentId = $entity->getId();

            $keywordsUsedInContent = $this->keywordRepository
                ->findByUsedInEntity($contentId, NodeInterface::ENTITY_TYPE);

            foreach ($keywordsUsedInContent as $keyword) {
                $keyword->removeUseInEntity($contentId, NodeInterface::ENTITY_TYPE);
            }
        }
    }
}
