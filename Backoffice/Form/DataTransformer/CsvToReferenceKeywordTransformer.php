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
        return $this->partialReverseTransform($keywords, false);
    }

    /**
     * @param mixed $keywords
     *
     * @return array
     */
    protected function getKeywordAsArray($keywords) {
        if ($keywords instanceof ArrayCollection) {
            $keywords = $keywords->toArray();
            foreach ($keywords as &$keyword) {
                $keyword = $keyword->getId();
            }

            return $keywords;
        }

        return explode(',', $keywords);
    }

    /**
     * @param string $keywords
     *
     * @return string
     */
    protected function getKeywordAsCondition($keywords) {
        $keywords = $keywords->toArray();
        foreach ($keywords as &$keyword) {
            $keyword = $keyword->getLabel();
        }

        return implode(',', $keywords);
    }
}
