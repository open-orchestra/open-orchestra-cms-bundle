<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Facade\Traits\BlameableFacade;

/**
 * Class ContentFacade
 */
class ContentFacade extends DeletedFacade
{
    use BlameableFacade;

    /**
     * @Serializer\Type("string")
     */
    public $contentType;

    /**
     * @Serializer\Type("integer")
     */
    public $version;

    /**
     * @Serializer\Type("integer")
     */
    public $contentTypeVersion;

    /**
     * @Serializer\Type("string")
     */
    public $language;

    /**
     * @Serializer\Type("OpenOrchestra\ApiBundle\Facade\StatusFacade")
     */
    public $status;

    /**
     * @Serializer\Type("string")
     */
    public $statusLabel;

    /**
     * @Serializer\Type("string")
     */
    public $statusId;

    /**
     * @Serializer\Type("boolean")
     */
    public $linkedToSite;

    /**
     * @Serializer\Type("array<string,OpenOrchestra\ApiBundle\Facade\ContentAttributeFacade>")
     */
    protected $attributes = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addAttribute(FacadeInterface $facade)
    {
        $this->attributes[$facade->name] = $facade;
    }
}
