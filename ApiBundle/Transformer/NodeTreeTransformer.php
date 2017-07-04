<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class NodeTreeTransformer
 */
class NodeTreeTransformer extends AbstractTransformer
{
    /**
     * @param array $nodeTree
     * @param array $params
     *
     * @return FacadeInterface
     */
    public function transform($nodeTree, array $params = array())
    {
        $facade = $this->newFacade();

        $facade->node = $this->getContext()->transform('node_tree_leaf', $nodeTree['node']);
        if (array_key_exists('child', $nodeTree)) {
            foreach ($nodeTree['child'] as $child) {
                $facade->addChild($this->getContext()->transform('node_tree', $child));
            }
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'node_tree';
    }
}
