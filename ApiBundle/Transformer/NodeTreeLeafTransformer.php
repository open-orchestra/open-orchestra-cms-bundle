<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class NodeTreeLeafTransformer
 */
class NodeTreeLeafTransformer extends AbstractTransformer
{
    /**
     * @param array $node
     *
     * @return FacadeInterface
     */
    public function transform($node)
    {
        $facade = $this->newFacade();

        $facade->nodeId = $node['nodeId'];
        $facade->name = $node['name'];
        $facade->siteId = $node['siteId'];

        return $facade;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'node_tree_leaf';
    }
}
