<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;

/**
 * Class ContentCollectionFacade
 */
class ContentCollectionFacade extends PaginateCollectionFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'contents';

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\ContentFacade>")
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
