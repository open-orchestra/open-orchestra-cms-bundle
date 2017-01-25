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
     *
     * @return FacadeInterface
     */
    public function transform($keywordCollection)
    {
        $facade = $this->newFacade();

        foreach ($keywordCollection as $keyword) {
            $facade->addKeyword($this->getTransformer('keyword')->transform($keyword));
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param null            $source
     *
     * @return array
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        $keywords = array();
        $keywordsFacade = $facade->getKeywords();
        foreach ($keywordsFacade as $keywordFacade) {
            $keyword = $this->getTransformer('keyword')->reverseTransform($keywordFacade);
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
