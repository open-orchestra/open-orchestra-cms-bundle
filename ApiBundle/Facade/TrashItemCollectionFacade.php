<?php

namespace OpenOrchestra\ApiBundle\Facade;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class TrashItemCollectionFacade
 */
class TrashItemCollectionFacade extends PaginateCollectionFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'trash_items';

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\TrashItemFacade>")
     */
    protected $trashItems = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addElement(FacadeInterface $facade)
    {
        $this->trashItems[] = $facade;
    }
}
