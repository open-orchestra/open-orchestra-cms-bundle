<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\LinkFacade;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class LinkTransformer
 */
class LinkTransformer extends AbstractTransformer
{
    /**
     * @param array $link
     *
     * @return FacadeInterface|LinkFacade
     */
    public function transform($link)
    {
        $facade = new LinkFacade();

        $facade->name = $link['name'];
        $facade->link = $link['link'];

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'link';
    }

}
