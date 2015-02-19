<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\LinkFacade;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;

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
