<?php

namespace PHPOrchestra\BackofficeBundle\Form\DataTransformer;

use PHPOrchestra\ModelBundle\Document\Keyword;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PHPOrchestra\ModelBundle\Document\EmbedKeyword;
use PHPOrchestra\ModelBundle\Repository\KeywordRepository;

/**
 * Class EmbedKeywordsToKeywordsTransformer
 */
class EmbedKeywordsToKeywordsTransformer implements DataTransformerInterface
{
    protected $keywordRepository;

    /**
     * @param KeywordRepository $keywordRepository
     */
    public function __construct(KeywordRepository $keywordRepository)
    {
        $this->keywordRepository = $keywordRepository;
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

        foreach($keywordArray as $keyword) {
            if ('' != $keywords) {
                $keywordEntity = $this->keywordRepository->findOneByLabel($keyword);
                if (!$keywordEntity) {
                    $keywordEntity = new Keyword();
                    $keywordEntity->setLabel($keyword);
                    $this->keywordRepository->getDocumentManager()->persist($keywordEntity);
                    $this->keywordRepository->getDocumentManager()->flush($keywordEntity);
                }
                $embedKeywords->add(EmbedKeyword::createFromKeyword($keywordEntity));
            }
        }

        return $embedKeywords;
    }
}
