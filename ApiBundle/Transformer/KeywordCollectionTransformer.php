<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\KeywordCollectionFacade;

/**
 * Class KeywordCollectionTransformer
 */
class KeywordCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new KeywordCollectionFacade();

        foreach ($mixed as $keyword) {
            $facade->addKeyword($this->getTransformer('keyword')->transform($keyword));
        }

        $facade->addLink('_self', $this->generateRoute(
            'php_orchestra_api_keyword_list',
            array()
        ));

        $facade->addLink('_self_add', $this->generateRoute(
            'php_orchestra_backoffice_keyword_new',
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
