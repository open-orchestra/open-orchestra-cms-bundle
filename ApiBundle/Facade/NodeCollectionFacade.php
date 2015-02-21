<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class NodeCollection
 */
class NodeCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'nodes';

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\NodeFacade>")
     */
    protected $nodes = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addNode(FacadeInterface $facade)
    {
        $this->nodes[] = $facade;
    }

    /**
     * @return mixed
     */
    public function getNodes()
    {
        return $this->nodes;
    }
}
