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
     * @param array $mixed
     *
     * @return FacadeInterface|LinkFacade
     */
    public function transform($mixed)
    {
        $link = new LinkFacade();

        $link->name = $mixed['name'];
        $link->link = $mixed['link'];

        return $link;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'link';
    }

}
