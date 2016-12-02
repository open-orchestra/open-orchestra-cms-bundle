<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

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
        $facade->language = $node['language'];
        $facade->version = $node['version'];
        $facade->siteId = $node['siteId'];
        $facade->order = $node['order'];
        $facade->status = $this->getTransformer('status_node_tree')->transform($node['status']);

        $facade->addRight('can_create', (NodeInterface::TYPE_DEFAULT === $node['nodeType']));

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
