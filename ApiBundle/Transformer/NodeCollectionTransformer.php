<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ApiBundle\Facade\NodeCollectionFacade;

/**
 * Class NodeCollectionTransformer
 */
class NodeCollectionTransformer extends AbstractTransformer
{
    /**
     * @param \Doctrine\Common\Collections\Collection $nodeCollection
     *
     * @return \OpenOrchestra\BaseApi\Facade\FacadeInterface
     */
    public function transformVersions($nodeCollection)
    {
        $facade = new NodeCollectionFacade();

        foreach ($nodeCollection as $node) {
            $facade->addNode($this->getTransformer('node')->transformVersion($node));
        }

        return $facade;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $nodeCollection
     *
     * @return array
     */
    public function reverseTransformOrder($nodeCollection)
    {
        $orderedNode = array();
        /** @var \OpenOrchestra\ApiBundle\Facade\NodeFacade $node */
        foreach ($nodeCollection->getNodes() as $node) {
            $orderedNode[] = $node->nodeId;
        }

        return $orderedNode;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'node_collection';
    }
}
