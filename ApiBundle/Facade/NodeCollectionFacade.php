<?php

namespace OpenOrchestra\ApiBundle\Facade;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

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
