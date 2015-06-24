<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ApiBundle\Facade\KeywordCollectionFacade;

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
