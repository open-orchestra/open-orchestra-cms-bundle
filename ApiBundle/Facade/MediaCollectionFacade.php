<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

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
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\MediaFacade>")
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
