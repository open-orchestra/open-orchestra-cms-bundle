<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\NodeTreeFacade;
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
        $facade = new NodeTreeFacade();

        $facade->node = $this->getTransformer('node')->transform($nodeCollection['node']);

        if (array_key_exists('child', $nodeCollection)) {
            foreach ($nodeCollection['child'] as $child) {
                $facade->addChild($this->getTransformer('node_tree')->transform($child));
            }
        }

        $facade->addLink('_role_list_node', $this->generateRoute(
            'open_orchestra_api_role_list_by_type',
            array('type' => 'node')
        ));

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
