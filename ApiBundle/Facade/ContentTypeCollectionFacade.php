<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class ContentTypeCollection
 */
class ContentTypeCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'content_types';

    /**
     * @Serializer\Type("array<PHPOrchestra\ApiBundle\Facade\ContentTypeFacade>")
     */
    protected $contentTypes = array();

    /**
     * @param FacadeInterface|ContentTypeFacade $facade
     */
    public function addContentType(FacadeInterface $facade)
    {
        $this->contentTypes[] = $facade;
    }
}
