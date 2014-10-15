<?php

namespace PHPOrchestra\ApiBundle\Facade;

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
     * @Serializer\Type("array<PHPOrchestra\ApiBundle\Facade\NodeFacade>")
     */
    protected $nodes = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addNode(FacadeInterface $facade)
    {
        $this->nodes[] = $facade;
    }
}
