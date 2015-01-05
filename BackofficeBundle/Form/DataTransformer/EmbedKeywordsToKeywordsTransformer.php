<?php

namespace PHPOrchestra\BackofficeBundle\Form\DataTransformer;

use PHPOrchestra\ModelInterface\Repository\KeywordRepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class EmbedKeywordsToKeywordsTransformer
 */
class EmbedKeywordsToKeywordsTransformer implements DataTransformerInterface
{
    protected $keywordRepository;
    protected $embedKeywordClass;
    protected $keywordClass;

    /**
     * @param KeywordRepositoryInterface $keywordRepository
     * @param string                     $embedKeywordClass
     * @param string                     $keywordClass
     */
    public function __construct(KeywordRepositoryInterface $keywordRepository, $embedKeywordClass, $keywordClass)
    {
        $this->keywordRepository = $keywordRepository;
        $this->embedKeywordClass = $embedKeywordClass;
        $this->keywordClass = $keywordClass;
    }

    /**
     * @param ArrayCollection $embedKeywords
     *
     * @return ArrayCollection
     */
    public function transform($embedKeywords)
    {
        if (null === $embedKeywords) {
            return '';
        }

        if (is_string($embedKeywords)) {
            return $embedKeywords;
        }

        $keyworks = array();
        foreach ($embedKeywords as $keyword) {
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
        $embedKeywords = new ArrayCollection();
        $embedKeywordClass = $this->embedKeywordClass;
        $keywordClass = $this->keywordClass;

        foreach($keywordArray as $keyword) {
            if ('' != $keywords) {
                $keywordEntity = $this->keywordRepository->findOneByLabel($keyword);
                if (!$keywordEntity) {
                    $keywordEntity = new $keywordClass();
                    $keywordEntity->setLabel($keyword);
                    $this->keywordRepository->getDocumentManager()->persist($keywordEntity);
                    $this->keywordRepository->getDocumentManager()->flush($keywordEntity);
                }
                $embedKeywords->add($embedKeywordClass::createFromKeyword($keywordEntity));
            }
        }

        return $embedKeywords;
    }
}
