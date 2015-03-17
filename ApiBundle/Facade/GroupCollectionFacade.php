<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class GroupCollection
 */
class GroupCollectionFacade extends AbstractFacade
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
