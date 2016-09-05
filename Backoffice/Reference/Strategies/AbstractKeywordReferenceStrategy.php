<?php

namespace OpenOrchestra\Backoffice\Reference\Strategies;

use OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\KeywordableInterface;

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
     */
    protected function extractKeywordIdsFromConditionAttribute(array $attribute)
    {
        if (!isset($attribute['keywords'])) {
            return array();
        }

        $string = $attribute['keywords'];
        $parsedString = str_replace(' ', '', $string);
        $parsedString = str_replace(array('(',')', 'AND', 'OR', 'NOT'), ' ', $parsedString);

        return explode(' ', $parsedString);
    }

    /**
     * @param ContentInterface $content
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
