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
     *
     * @return FacadeInterface
     */
    public function transform($nodesTree)
    {
        $tree = array();
        foreach ($nodesTree as $node) {
            $tree[] = $this->getTransformer('node_tree')->transform($node);
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
