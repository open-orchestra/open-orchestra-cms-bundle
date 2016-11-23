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
     *
     * @return FacadeInterface
     */
    public function transform($nodeTree)
    {
        $facade = $this->newFacade();

        $facade->node = $this->getTransformer('node_tree_leaf')->transform($nodeTree['node']);
        if (array_key_exists('child', $nodeTree)) {
            foreach ($nodeTree['child'] as $child) {
                $facade->addChild($this->getTransformer('node_tree')->transform($child));
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
