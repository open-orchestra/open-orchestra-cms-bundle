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
     * @param array $nodeCollection
     *
     * @return FacadeInterface
     */
    public function transform($nodeCollection)
    {
        $facade = $this->newFacade();

        $facade->node = $this->getTransformer('node')->transform($nodeCollection['node']);

        if (array_key_exists('child', $nodeCollection)) {
            foreach ($nodeCollection['child'] as $child) {
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
