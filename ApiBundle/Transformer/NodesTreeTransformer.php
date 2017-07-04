<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class NodesTreeTransformer
 */
class NodesTreeTransformer extends AbstractTransformer
{
    /**
     * @param array $nodesTree
     * @param array $params
     *
     * @return FacadeInterface
     */
    public function transform($nodesTree, array $params = array())
    {
        $tree = array();
        foreach ($nodesTree as $node) {
            $tree[] = $this->getContext()->transform('node_tree', $node);
        }

        return $tree;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'nodes_tree';
    }
}
