<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class KeywordCollectionTransformer
 */
class KeywordCollectionTransformer extends AbstractTransformer
{
    /**
     * @param Collection $keywordCollection
     * @param array      $params
     *
     * @return FacadeInterface
     */
    public function transform($keywordCollection, array $params = array())
    {
        $facade = $this->newFacade();

        foreach ($keywordCollection as $keyword) {
            $facade->addKeyword($this->getContext()->transform('keyword', $keyword));
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param array           $params
     *
     * @return array
     */
    public function reverseTransform(FacadeInterface $facade, array $params = array())
    {
        $keywords = array();
        $keywordsFacade = $facade->getKeywords();
        foreach ($keywordsFacade as $keywordFacade) {
            $keyword = $this->getContext()->reverseTransform('keyword', $keywordFacade);
            if (null !== $keyword) {
                $keywords[] = $keyword;
            }
        }

        return $keywords;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'keyword_collection';
    }
}
