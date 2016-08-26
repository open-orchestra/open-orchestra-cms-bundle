<?php

namespace OpenOrchestra\Backoffice\Reference\Strategies;

use OpenOrchestra\ModelInterface\Model\ContentInterface;

/**
 * Class KeywordInContentReferenceStrategy
 */
class KeywordInContentReferenceStrategy extends AbstractKeywordReferenceStrategy implements ReferenceStrategyInterface
{
    /**
     * @param mixed $entity
     *
     * @return boolean
     */
    public function support($entity)
    {
        return ($entity instanceof ContentInterface);
    }

    /**
     * @param mixed $entity
     */
    public function addReferencesToEntity($entity)
    {
        $keywordIds = $this->extractKeywordsFromKeywordableEntity($entity);

        foreach ($keywordIds as $keywordId) {
            /** @var OpenOrchestra\ModelInterface\Model\KeywordInterface $keyword */
            $keyword = $this->keywordRepository->find($keywordId);
            if ($keyword) {
                $keyword->addUseInEntity($entity->getId(), ContentInterface::ENTITY_TYPE);
            }
        }
    }

    /**
     * @param mixed $entity
     */
    public function removeReferencesToEntity($entity)
    {
        $contentId = $entity->getId();

        $keywordsUsedInContent = $this->keywordRepository
            ->findByUsedInEntity($contentId, ContentInterface::ENTITY_TYPE);

        foreach ($keywordsUsedInContent as $keyword) {
            $keyword->removeUseEntity($contentId, ContentInterface::ENTITY_TYPE);
        }
    }
}
