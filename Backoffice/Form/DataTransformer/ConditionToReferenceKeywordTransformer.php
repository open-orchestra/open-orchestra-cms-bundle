<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use OpenOrchestra\Backoffice\Manager\KeywordToDocumentManager;
use OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\Backoffice\Exception\NotFoundedKeywordException;

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
     *
     * @throws NotFoundedKeywordException
     */
    public function transform($keywords)
    {
        if (null === $keywords) {
            return '';
        }

        $keywordWithoutOperator = preg_replace(ContentRepositoryInterface::OPERATOR_SPLIT, ' ', $keywords);
        $keywordArray = explode(' ', $keywordWithoutOperator);

        foreach ($keywordArray as $keyword) {
            if ($keyword != '') {
                $keywordDocument = $this->keywordRepository->find($keyword);
                if (!is_null($keywordDocument)) {
                    $keywords = str_replace($keyword, $keywordDocument->getLabel(), $keywords);
                } else {
                    throw new NotFoundedKeywordException();
                }
            }
        }

        return $keywords;
    }

    /**
     * @param string $keywords
     *
     * @return string
     *
     * @throws NotFoundedKeywordException
     */
    public function reverseTransform($keywords)
    {
        $keywordWithoutOperator = preg_replace(ContentRepositoryInterface::OPERATOR_SPLIT, ' ', $keywords);
        $keywordArray = explode(' ', $keywordWithoutOperator);

        foreach ($keywordArray as $keyword) {
            if ($keyword != '') {
                $keywordDocument = $this->keywordToDocumentManager->getDocument($keyword);
                if($keywordDocument instanceof KeywordInterface) {
                    $keywords = str_replace($keyword, $keywordDocument->getId(), $keywords);
                } else {
                    throw new NotFoundedKeywordException();
                }
            }
        }

        return $keywords;
    }
}
