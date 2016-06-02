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
        return $this->replaceKeywords($keywords, 'getLabelFromRepository');
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
        return $this->replaceKeywords($keywords, 'getIdFromManager');
    }
    /**
     * @param string $keywords
     *
     * @return string
     *
     * @throws NotFoundedKeywordException
     */
    protected function replaceKeywords($keywords, $method) {
        $keywordWithoutOperator = preg_replace(ContentRepositoryInterface::OPERATOR_SPLIT, ' ', $keywords);
        $keywordArray = explode(' ', $keywordWithoutOperator);
        foreach ($keywordArray as $keyword) {
            if ($keyword != '') {
                if (null !== ($value = $this->$method)) {
                    $keywords = str_replace($keyword, $value, $keywords);
                } else {
                    throw new NotFoundedKeywordException();
                }
            }
        }
        return $keywords;
    }
    /**
     * @param string $keyword
     *
     * @return string|null
     */
    protected function getLabelFromRepository($keyword) {
        $keywordDocument = $this->keywordRepository->find($keyword);
        if($keywordDocument instanceof KeywordInterface) {
            return $keywordDocument->getLabel();
        }
        return null;
    }
     /**
     * @param string $keyword
     *
     * @return string|null
     */
    protected function getIdFromManager($keyword) {
        $keywordDocument = $this->keywordToDocumentManager->getDocument($keyword);
        if($keywordDocument instanceof KeywordInterface) {
            return $keywordDocument->getId();
        }
        return null;
    }
}