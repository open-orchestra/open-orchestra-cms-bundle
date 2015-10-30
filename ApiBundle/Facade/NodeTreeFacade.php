<?php

namespace OpenOrchestra\ApiBundle\Facade;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

/**
 * Class NodeTreeFacade
 */
class NodeTreeFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'childs';

    /**
     * @Serializer\Type("OpenOrchestra\ApiBundle\Facade\NodeFacade")
     */
    public $node;

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\NodeTreeFacade>")
     */
    protected $childs = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addChild(FacadeInterface $facade)
    {
        $this->childs[] = $facade;
    }

    /**
     * @return array
     */
    public function getChilds()
    {
        return $this->childs;
    }
}
