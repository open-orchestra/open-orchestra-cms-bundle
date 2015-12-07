<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ApiBundle\Facade\NodeCollectionFacade;
use OpenOrchestra\ApiBundle\Facade\NodeFacade;

/**
 * Class NodeCollectionTransformer
 */
class NodeCollectionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param Collection $nodeCollection
     *
     * @return FacadeInterface
     */
    public function transform($nodeCollection)
    {
        $facade = new NodeCollectionFacade();

        foreach ($nodeCollection as $node) {
            if ($this->authorizationChecker->isGranted(TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE, $node)) {
                $facade->addNode($this->getTransformer('node')->transform($node));
            }
        }

        return $facade;
    }

    /**
     * @param Collection $nodeCollection
     *
     * @return FacadeInterface
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
     * @param Collection $nodeCollection
     *
     * @return array
     */
    public function reverseTransformOrder($nodeCollection)
    {
        $orderedNode = array();
        /** @var NodeFacade $node */
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
