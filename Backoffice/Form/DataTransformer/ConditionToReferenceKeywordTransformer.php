<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use OpenOrchestra\Backoffice\Manager\KeywordToDocumentManager;
use OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface;
use OpenOrchestra\Backoffice\Exception\NotFoundedKeywordException;
use OpenOrchestra\ModelInterface\Repository\RepositoryTrait\KeywordableTraitInterface;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;

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
    ){
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

        $keywordArray = $this->getKeywordAsArray($keywords);

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
        $keywordArray = $this->getKeywordAsArray($keywords);

        foreach ($keywordArray as $keyword) {
            if ($keyword != '') {
                $keywordDocument = $this->keywordToDocumentManager->getDocument($keyword);
                if ($keywordDocument instanceof KeywordInterface) {
                    $keywords = str_replace($keyword, $keywordDocument->getId(), $keywords);
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
     * @return array
     */
    protected function getKeywordAsArray($keywords) {
        $keywordWithoutOperator = preg_replace(explode('|', KeywordableTraitInterface::OPERATOR_SPLIT), ' ', $keywords);
        $keywordArray = explode(' ', $keywordWithoutOperator);

        return $keywordArray;
    }
}
