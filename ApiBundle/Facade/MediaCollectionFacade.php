<?php

namespace PHPOrchestra\ApiBundle\Facade;

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
     * @Serializer\Type("array<PHPOrchestra\ApiBundle\Facade\MediaFacade>")
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
