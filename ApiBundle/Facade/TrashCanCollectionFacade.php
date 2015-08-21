<?php

namespace OpenOrchestra\ApiBundle\Facade;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class TrashCanCollection
 */
class TrashCanCollectionFacade extends PaginateCollectionFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'trashcan';

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\TrashCanFacade>")
     */
    protected $trashcan = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addElement(FacadeInterface $facade)
    {
        $this->trashcan[] = $facade;
    }
}
