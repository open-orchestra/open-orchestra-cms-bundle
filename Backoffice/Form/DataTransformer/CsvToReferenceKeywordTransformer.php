<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class CsvToReferenceKeywordTransformer
 */
class CsvToReferenceKeywordTransformer extends AbstractReferenceKeywordTransformer
{
    /**
     * @param string $keywords
     *
     * @return ArrayCollection
     */
    public function reverseTransform($keywords)
    {
        $keywords = explode(',', $this->partialReverseTransform($keywords));

        return new ArrayCollection($keywords);
    }

    /**
     * @param mixed $keywords
     *
     * @return array
     */
    protected function getKeywordAsArray($keywords) {
        return ($keywords instanceof ArrayCollection) ? $keywords->toArray() : explode(',', $keywords);
    }

    /**
     * @param string $keywords
     *
     * @return string
     */
    protected function getKeywordAsCondition($keywords) {
        return implode(',', $keywords->toArray());
    }
}
