<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class LinkTransformer
 */
class LinkTransformer extends AbstractTransformer
{
    /**
     * @param array      $link
     * @param array|null $params
     *
     * @return FacadeInterface
     */
    public function transform($link, array $params = null)
    {
        $facade = $this->newFacade();

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
