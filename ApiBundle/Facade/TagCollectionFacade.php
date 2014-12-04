<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class TagCollection
 */
class TagCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'tags';

    /**
     * @Serializer\Type("array<PHPOrchestra\ApiBundle\Facade\TagFacade>")
     */
    protected $tags = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addTag(FacadeInterface $facade)
    {
        $this->tags[] = $facade;
    }
}
