<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ApiBundle\Facade\KeywordCollectionFacade;

/**
 * Class KeywordCollectionTransformer
 */
class KeywordCollectionTransformer extends AbstractTransformer
{
    /**
     * @param \Doctrine\Common\Collections\Collection $keywordCollection
     *
     * @return \OpenOrchestra\BaseApi\Facade\FacadeInterface
     */
    public function transform($keywordCollection)
    {
        $facade = new KeywordCollectionFacade();

        foreach ($keywordCollection as $keyword) {
            $facade->addKeyword($this->getTransformer('keyword')->transform($keyword));
        }

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_keyword_list',
            array()
        ));

        $facade->addLink('_self_add', $this->generateRoute(
            'open_orchestra_backoffice_keyword_new',
            array()
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'keyword_collection';
    }
}
