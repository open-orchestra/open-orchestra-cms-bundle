<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;

/**
 * Class ContentTypeCollection
 */
class ContentTypeCollectionFacade extends PaginateCollectionFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'content_types';

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\ContentTypeFacade>")
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
