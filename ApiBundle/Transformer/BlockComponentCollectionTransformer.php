<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class BlockComponentCollectionTransformer
 */
class BlockComponentCollectionTransformer extends AbstractTransformer
{
    /**
     * @param array $blockComponentCollection
     * @param array $params
     *
     * @return FacadeInterface
     */
    public function transform($blockComponentCollection, array $params = array())
    {
        $facade = $this->newFacade();

        foreach ($blockComponentCollection as $blockComponent) {
            $facade->addBlockComponents($this->getContext()->transform('block_component', $blockComponent));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'block_component_collection';
    }
}
