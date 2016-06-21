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
            $keywordDocument = $this->keywordRepository->find($keyword);
            if ($keywordDocument instanceof KeywordInterface) {
                $referenceKeywords->add($keywordDocument);
            } else {
                throw new NotFoundedKeywordException();
            }
        }

        return $referenceKeywords;
    }

    /**
     * @param string $keywords
     *
     * @return array
     */
    protected function getKeywordAsArray($keywords) {
        return explode(',', $keywords);
    ;
    }
}
