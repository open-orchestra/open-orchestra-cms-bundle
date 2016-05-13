<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\Backoffice\Manager\KeywordToDocumentManager;

/**
 * Class ReferencedKeywordsToKeywordsTransformer
 */
class ReferencedKeywordsToKeywordsTransformer implements DataTransformerInterface
{
    protected $keywordToDocumentManager;

    /**
     * @param KeywordToDocumentManager $keywordToDocumentManager
     */
    public function __construct(
        KeywordToDocumentManager $keywordToDocumentManager
    )
    {
        $this->keywordToDocumentManager = $keywordToDocumentManager;    }

    /**
     * @param ArrayCollection $referenceKeywords
     *
     * @return ArrayCollection
     */
    public function transform($referenceKeywords)
    {
        if (null === $referenceKeywords) {
            return '';
        }

        $keyworks = array();
        foreach ($referenceKeywords as $keyword) {
            $keyworks[] = $keyword->getLabel();
        }

        return implode(',', $keyworks);
    }

    /**
     * @param string $keywords
     *
     * @return ArrayCollection
     */
    public function reverseTransform($keywords)
    {
        $keywordArray = explode(',', $keywords);
        $referenceKeywords = new ArrayCollection();

        foreach($keywordArray as $keyword) {
            $referenceKeywords->add($this->keywordToDocumentManager->getDocument($keyword));
        }

        return $referenceKeywords;
    }
}
