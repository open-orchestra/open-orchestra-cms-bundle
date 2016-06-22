<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

use OpenOrchestra\ModelInterface\Repository\RepositoryTrait\KeywordableTraitInterface;

/**
 * Class ConditionToReferenceKeywordTransformer
 */
class ConditionToReferenceKeywordTransformer extends AbstractReferenceKeywordTransformer
{
    /**
     * @param string $keywords
     *
     * @return string
     */
    public function reverseTransform($keywords)
    {
        return $this->partialReverseTransform($keywords);
    }

    /**
     * @param string $keywords
     *
     * @return array
     */
    protected function getKeywordAsArray($keywords) {
        $keywordWithoutOperator = preg_replace(explode('|', KeywordableTraitInterface::OPERATOR_SPLIT), ' ', $keywords);
        $keywordArray = explode(' ', $keywordWithoutOperator);

        return $keywordArray;
    }

    /**
     * @param string $keywords
     *
     * @return string
     */
    protected function getKeywordAsCondition($keywords) {
        return $keywords;
    }
}
