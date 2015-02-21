<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ApiBundle\Facade\NodeCollectionFacade;
use OpenOrchestra\ApiBundle\Facade\NodeFacade;

/**
 * Class NodeCollectionTransformer
 */
class NodeCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection $mixed
     *
     * @return FacadeInterface
     */
    public function transformVersions($mixed)
    {
        $facade = new NodeCollectionFacade();

        foreach ($mixed as $node) {
            $facade->addNode($this->getTransformer('node')->transformVersion($node));
        }

        return $facade;
    }

    /**
     * @param NodeCollectionFacade $mixed
     *
     * @return array
     */
    public function reverseTransformOrder($mixed)
    {
        $orderedNode = array();
        /** @var NodeFacade $node */
        foreach ($mixed->getNodes() as $node) {
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
