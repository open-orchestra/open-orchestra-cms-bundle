<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\Backoffice\Manager\KeywordToDocumentManager;
use OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface;

/**
 * Class ConditionToReferenceKeywordTransformer
 */
class ConditionToReferenceKeywordTransformer implements DataTransformerInterface
{
    const OPERATOR_SPLIT = array('/ *\( +/', '/ +\) */', '/ *NOT +/', '/ +AND +/', '/ +OR +/');
    const SEPARATOR = '##';

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
     * @return ArrayCollection
     */
    public function transform($keywords)
    {
        if (null === $keywords) {
            return '';
        }
        preg_match_all('/' . self::SEPARATOR . '(.*?)' . self::SEPARATOR . '/', $keywords, $matches);
        foreach ($matches[1] as $id) {
            $keywordDocument = $this->keywordRepository->find($id);
            if (!is_null($keywordDocument)) {
                $keywords = str_replace(self::SEPARATOR . $id . self::SEPARATOR, $keywordDocument->getLabel(), $keywords);
            } else {
                return '';
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
        $keywordWithoutOperator = preg_replace(self::OPERATOR_SPLIT, ' ', $keywords);
        $keywordArray = explode(' ', $keywordWithoutOperator);

        foreach ($keywordArray as $keyword) {
            if ($keyword != '') {
                $keywordDocument = $this->keywordToDocumentManager->getDocument($keyword);
                $keywords = str_replace($keyword, self::SEPARATOR . $keywordDocument->getId() . self::SEPARATOR, $keywords);
            }
        }

        return $keywords;
    }
}
