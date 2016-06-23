<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface;
use OpenOrchestra\Backoffice\Exception\NotFoundedKeywordException;
use OpenOrchestra\Backoffice\Manager\KeywordToDocumentManager;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class AbstractReferenceKeywordTransformer
 */
abstract class AbstractReferenceKeywordTransformer implements DataTransformerInterface
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

        $keywordCondition = $this->getKeywordAsCondition($keywords);
        $keywordArray = $this->getKeywordAsArray($keywords);

        foreach ($keywordArray as $keyword) {
            if ($keyword != '') {
                $keywordDocument = $this->keywordRepository->find($keyword);
                if (!is_null($keywordDocument)) {
                    $keywords = str_replace($keyword, $keywordDocument->getLabel(), $keywordCondition);
                } else {
                    throw new NotFoundedKeywordException();
                }
            }
        }

        return $keywordCondition;
    }

    /**
     * @param string  $keywords
     * @param boolean $asString
     *
     * @return mixed
     *
     * @throws NotFoundedKeywordException
     */
    protected function partialReverseTransform($keywords, $asString = true)
    {
        $keywordArray = $this->getKeywordAsArray($keywords);
        $referenceKeywords = new ArrayCollection();

        foreach ($keywordArray as $keyword) {
            if ($keyword != '') {
                $keywordDocument = $this->keywordRepository->find($keyword);
                if (is_null($keywordDocument)) {
                    $keywordDocument = $this->keywordToDocumentManager->getDocument($keyword);
                }
                if (!is_null($keywordDocument)) {
                    $keywords = str_replace($keyword, $keywordDocument->getId(), $keywords);
                    $referenceKeywords->add($keywordDocument);
                } else {
                    throw new NotFoundedKeywordException();
                }
            }
        }

        return $asString ? $keywords : $referenceKeywords;
    }

    /**
     * @param mixed $keywords
     *
     * @return array
     */
    abstract protected function getKeywordAsArray($keywords);

    /**
     * @param string $keywords
     *
     * @return string
     */
    abstract protected function getKeywordAsCondition($keywords);
}
