<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

use OpenOrchestra\Backoffice\Exception\NotFoundedKeywordException;
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
     *
     * @throws NotFoundedKeywordException
     */
    public function reverseTransform($keywords)
    {
        return $keywords;
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
}
