<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\NodeCollectionFacade;
use PHPOrchestra\ApiBundle\Facade\NodeFacade;

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
