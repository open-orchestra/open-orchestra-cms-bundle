<?php

namespace PHPOrchestra\BackofficeBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PHPOrchestra\ModelBundle\Document\EmbedKeyword;
use PHPOrchestra\ModelBundle\Repository\KeywordRepository;

/**
 * Class EmbedKeywordsToKeywordsTransformer
 */
class EmbedKeywordsToKeywordsTransformer implements DataTransformerInterface
{
    var $keywordRepository;

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
            return array();
        }

        $keywords = new ArrayCollection();

        foreach($embedKeywords as $embed) {
            $keyword = $this->keywordRepository->find($embed->getId());
            if ($keyword) {
                $keywords->add($keyword);
            }
        }

        return $keywords;
    }

    /**
     * @param ArrayCollection $keywords
     *
     * @return ArrayCollection
     */
    public function reverseTransform($keywords)
    {
        $embedKeywords = new ArrayCollection();

        foreach($keywords as $keyword) {
            $embedKeywords->add(EmbedKeyword::createFromKeyword($keyword));
        }

        return $embedKeywords;
    }
}
