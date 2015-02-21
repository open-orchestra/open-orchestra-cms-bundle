<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class DeletedCollection
 */
class DeletedCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'deleteds';

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\DeletedFacade>")
     */
    protected $deleteds = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addDeleted(FacadeInterface $facade)
    {
        $this->deleteds[] = $facade;
    }

    /**
     * @return mixed
     */
    public function getDeleteds()
    {
        return $this->deleteds;
    }

    /**
     * @param mixed $deleteds
     */
    public function setDeleteds($deleteds)
    {
        $this->deleteds = $deleteds;
    }
}
