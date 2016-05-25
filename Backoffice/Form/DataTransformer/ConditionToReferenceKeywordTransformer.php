<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\Backoffice\Manager\KeywordToDocumentManager;
use OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface;
use OpenOrchestra\ModelInterface\Form\DataTransformer\ConditionFromBooleanToBddTransformerInterface;

/**
 * Class ConditionToReferenceKeywordTransformer
 */
class ConditionToReferenceKeywordTransformer implements DataTransformerInterface
{
    protected $keywordToDocumentManager;
    protected $keywordRepository;

    /**
     * @param KeywordToDocumentManager   $keywordToDocumentManager
     * @param KeywordRepositoryInterface $keywordRepository
     */
    public function __construct(
        KeywordToDocumentManager $keywordToDocumentManager,
        KeywordRepositoryInterface $keywordRepository
    )
    {
        $this->keywordToDocumentManager = $keywordToDocumentManager;
        $this->keywordRepository = $keywordRepository;
    }

    /**
     * @param string $keywords
     *
     * @return string
     */
    public function transform($keywords)
    {
        if (null === $keywords) {
            return '';
        }

        $keywordWithoutOperator = preg_replace(ConditionFromBooleanToBddTransformerInterface::OPERATOR_SPLIT, ' ', $keywords);
        $keywordArray = explode(' ', $keywordWithoutOperator);

        foreach ($keywordArray as $keyword) {
            if ($keyword != '') {
                $keywordDocument = $this->keywordRepository->find($keyword);
                if (!is_null($keywordDocument)) {
                    $keywords = str_replace($keyword, $keywordDocument->getLabel(), $keywords);
                } else {
                    return '';
                }
            }
        }

        return $keywords;
    }

    /**
     * @param string $keywords
     *
     * @return string
     */
    public function reverseTransform($keywords)
    {
        $keywordWithoutOperator = preg_replace(ConditionFromBooleanToBddTransformerInterface::OPERATOR_SPLIT, ' ', $keywords);
        $keywordArray = explode(' ', $keywordWithoutOperator);

        foreach ($keywordArray as $keyword) {
            if ($keyword != '') {
                $keywordDocument = $this->keywordToDocumentManager->getDocument($keyword);
                $keywords = str_replace($keyword, $keywordDocument->getId(), $keywords);
            }
        }

        return $keywords;
    }
}
