<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class ContentCollectionFacade
 */
class ContentCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'contents';

    /**
     * @Serializer\Type("array<PHPOrchestra\ApiBundle\Facade\ContentFacade>")
     */
    protected $contents = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addContent(FacadeInterface $facade)
    {
        $this->contents[] = $facade;
    }
}
