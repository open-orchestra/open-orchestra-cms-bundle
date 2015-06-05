<?php

namespace OpenOrchestra\ApiBundle\Facade;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

/**
 * Class GroupCollection
 */
class GroupCollectionFacade extends PaginateCollectionFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'groups';

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\GroupFacade>")
     */
    protected $groups = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addGroup(FacadeInterface $facade)
    {
        $this->groups[] = $facade;
    }
}
