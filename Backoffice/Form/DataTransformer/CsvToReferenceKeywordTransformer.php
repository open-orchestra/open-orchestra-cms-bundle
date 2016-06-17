<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\Backoffice\Manager\KeywordToDocumentManager;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use OpenOrchestra\Backoffice\Exception\NotFoundedKeywordException;

/**
 * Class CsvToReferenceKeywordTransformer
 */
class CsvToReferenceKeywordTransformer implements DataTransformerInterface
{
    protected $keywordToDocumentManager;

    /**
     * @param KeywordToDocumentManager $keywordToDocumentManager
     */
    public function __construct(KeywordToDocumentManager $keywordToDocumentManager)
    {
        $this->keywordToDocumentManager = $keywordToDocumentManager;
    }

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

        $keywords = array();
        foreach ($referenceKeywords as $keyword) {
            $keywords[] = $keyword->getLabel();
        }

        return implode(',', $keywords);
    }

    /**
     * @param string $keywords
     *
     * @return ArrayCollection
     *
     * @throws NotFoundedKeywordException
     */
    public function reverseTransform($keywords)
    {
        $keywordArray = explode(',', $keywords);
        $referenceKeywords = new ArrayCollection();

        foreach ($keywordArray as $keyword) {
            $keywordDocument = $this->keywordToDocumentManager->getDocument($keyword);
            if ($keywordDocument instanceof KeywordInterface) {
                $referenceKeywords->add($keywordDocument);
            } else {
                throw new NotFoundedKeywordException();
            }
        }

        return $referenceKeywords;
    }
}