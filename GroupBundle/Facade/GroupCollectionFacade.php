<?php

namespace OpenOrchestra\GroupBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ApiBundle\Facade\PaginateCollectionFacade;

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
     * @Serializer\Type("array<OpenOrchestra\GroupBundle\Facade\GroupFacade>")
     */
    protected $groups = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addGroup(FacadeInterface $facade)
    {
        $this->groups[] = $facade;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }
}
