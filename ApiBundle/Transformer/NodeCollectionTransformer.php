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
     * @param array|null $params
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($nodeCollection, array $params = null)
    {
        $facade = $this->newFacade();

        foreach ($nodeCollection as $node) {
            $facade->addNode($this->getContetx()->transform('node', $node));
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param array|null      $params
     *
     * @return NodeInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, array $params = null)
    {
        $nodes = array();
        $nodesFacade = $facade->getNodes();
        foreach ($nodesFacade as $nodeFacade) {
            $node = $this->getContext()->reverseTransform('node', $nodeFacade);
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
