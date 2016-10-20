<?php

namespace OpenOrchestra\Backoffice\Reference\Strategies;

use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
use OpenOrchestra\ModelInterface\Model\BlockInterface;

/**
 * Class KeywordInBlockReferenceStrategy
 */
class KeywordInBlockReferenceStrategy extends AbstractKeywordReferenceStrategy implements ReferenceStrategyInterface
{
    /**
     * @param mixed $entity
     *
     * @return boolean
     */
    public function support($entity)
    {
        return ($entity instanceof ReadBlockInterface);
    }

    /**
     * @param mixed $entity
     */
    public function addreferencesToEntity($entity)
    {
        if ($this->support($entity)) {
            $keywordIds = $this->extractKeywordsFromElement($entity->getAttributes());
            foreach ($keywordIds as $keywordId) {
                /** @var OpenOrchestra\ModelInterface\Model\KeywordInterface $keyword */
                $keyword = $this->keywordRepository->find($keywordId);
                if ($keyword) {
                    $keyword->addUseInEntity($entity->getId(), BlockInterface::ENTITY_TYPE);
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
            $blockId = $entity->getId();

            $keywordsUsedInNode = $this->keywordRepository->findByUsedInEntity($blockId, BlockInterface::ENTITY_TYPE);

            foreach ($keywordsUsedInNode as $keyword) {
                $keyword->removeUseInEntity($blockId, BlockInterface::ENTITY_TYPE);
            }
        }
    }
}
