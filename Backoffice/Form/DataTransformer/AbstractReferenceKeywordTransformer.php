<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface;
use OpenOrchestra\Backoffice\Exception\NotFoundedKeywordException;

/**
 * Class ConditionToReferenceKeywordTransformer
 */
abstract class AbstractReferenceKeywordTransformer implements DataTransformerInterface
{
    protected $keywordRepository;

    /**
     * @param KeywordRepositoryInterface $keywordRepository
     */
    public function __construct(KeywordRepositoryInterface $keywordRepository) {
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
}
