<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

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
