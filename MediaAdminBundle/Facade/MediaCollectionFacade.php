<?php

namespace OpenOrchestra\MediaAdminBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;

/**
 * Class MediaCollection
 */
class MediaCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'medias';

    /**
     * @Serializer\Type("string")
     */
    public $parentId;

    /**
     * @Serializer\Type("boolean")
     */
    public $isFolderDeletable;

    /**
     * @Serializer\Type("array<OpenOrchestra\MediaAdminBundle\Facade\MediaFacade>")
     */
    protected $medias = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addMedia(FacadeInterface $facade)
    {
        $this->medias[] = $facade;
    }
}
