<?php

namespace OpenOrchestra\Backoffice\Reference\Strategies;

use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;

/**
 * Class KeywordInContentTypeReferenceStrategy
 */
class KeywordInContentTypeReferenceStrategy extends AbstractKeywordReferenceStrategy implements ReferenceStrategyInterface
{
    /**
     * @param mixed $entity
     *
     * @return boolean
     */
    public function support($entity)
    {
        return $entity instanceof ContentTypeInterface;
    }

    /**
     * @param mixed $entity
     */
    public function addReferencesToEntity($entity)
    {
        if ($this->support($entity)) {
            $keywordIds = $this->extractKeywordsFromContentType($entity);

            foreach ($keywordIds as $keywordId) {
                /** @var OpenOrchestra\ModelInterface\Model\KeywordInterface $keyword */
                $keyword = $this->keywordRepository->find($keywordId);
                if ($keyword) {
                    $keyword->addUseInEntity($entity->getId(), ContentTypeInterface::ENTITY_TYPE);
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
            $contentTypeId = $entity->getId();

            $keywordsUsedInContentType = $this->keywordRepository
                ->findByUsedInEntity($contentTypeId, ContentTypeInterface::ENTITY_TYPE);

            foreach ($keywordsUsedInContentType as $keyword) {
                $keyword->removeUseInEntity($contentTypeId, ContentTypeInterface::ENTITY_TYPE);
            }
        }
    }

    /**
     * @param ContentInterface $content
     *
     * @return array
     */
    protected function extractKeywordsFromContentType(ContentTypeInterface $contentType)
    {
        $keywordIds = array();
        $fields = $contentType->getFields();

        foreach ($fields as $field) {
            $fieldOptions = $field->getOptions();
            foreach ($fieldOptions as $option) {
                if ($this->isKeywordsConditionAttribute($option->getValue())) {
                    $keywordIds = array_merge(
                        $keywordIds,
                        $this->extractKeywordIdsFromConditionAttribute($option->getValue())
                    );
                }
            }
        }

        return $keywordIds;
    }
}
