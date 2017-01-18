<?php

namespace OpenOrchestra\Backoffice\Reference\Strategies;

use OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\KeywordableInterface;
use OpenOrchestra\ModelInterface\Repository\RepositoryTrait\KeywordableTraitInterface;

/**
 * Class AbstractKeywordReferenceStrategy
 */
abstract class AbstractKeywordReferenceStrategy
{
    protected $keywordRepository;

    /**
     * @param KeywordRepositoryInterface $keywordRepository
     */
    public function __construct(KeywordRepositoryInterface $keywordRepository)
    {
        $this->keywordRepository = $keywordRepository;
    }

    /**
     * Recursively extract keyword ids from elements (bloc, attribute, collection attribute, etc ...)
     *
     * @param mixed $element
     * @param array $references
     *
     * @return array
     */
    protected function extractKeywordsFromElement($element, array $references = array())
    {
        if ($this->isKeywordsConditionAttribute($element)) {
            $keywordIds = $this->extractKeywordIdsFromConditionAttribute($element);
            foreach ($keywordIds as $keywordId) {
                if ($keywordId != '') {
                    $references[] = $keywordId;
                }
            }
        } elseif (is_array($element)) {
            foreach ($element as $item) {
                $references = $this->extractKeywordsFromElement($item, $references);
            }
        }

        return $references;
    }

    /**
     * Check if $attributeValue matches with a keyword attribute
     *
     * @param mixed $attributeValue
     *
     * @return boolean
     */
    protected function isKeywordsConditionAttribute($attributeValue)
    {
        return is_array($attributeValue)
        && isset($attributeValue['keywords'])
        && is_string($attributeValue['keywords']);
    }

    /**
     * @param array $attribute
     *
     * @return array
     */
    protected function extractKeywordIdsFromConditionAttribute(array $attribute)
    {
        if (!isset($attribute['keywords'])) {
            return array();
        }

        $condition = $attribute['keywords'];
        $patterns = explode('|', KeywordableTraitInterface::OPERATOR_SPLIT);
        $conditionWithoutOperator = preg_replace($patterns, ' ', $condition);
        $keywordArray = explode(' ', $conditionWithoutOperator);

        return $keywordArray;
    }

    /**
     * @param KeywordableInterface $entity
     *
     * @return array
     */
    protected function extractKeywordsFromKeywordableEntity(KeywordableInterface $entity)
    {
        $references = array();
        $keywords = $entity->getKeywords();

        foreach ($keywords as $keyword) {
            $references[] = $keyword->getId();
        }

        return $references;
    }
}
