<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class NodeCollectionTransformer
 */
class NodeCollectionTransformer extends AbstractTransformer
{
    /**
     * @param Collection $nodeCollection
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($nodeCollection)
    {
        $facade = $this->newFacade();

        foreach ($nodeCollection as $node) {
            $facade->addNode($this->getTransformer('node')->transform($node));
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

        foreach ($nodeCollection->getNodes() as $node) {
            $orderedNode[] = $node->nodeId;
        }

        return $orderedNode;
    }

    /**
     * @param FacadeInterface $facade
     * @param null $source
     *
     * @return NodeInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        $nodes = array();
        $nodesFacade = $facade->getNodes();
        foreach ($nodesFacade as $nodeFacade) {
            $node = $this->getTransformer('node')->reverseTransform($nodeFacade);
            if (null !== $node) {
                $nodes[] = $node;
            }
        }

        return $nodes;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'node_collection';
    }
}
