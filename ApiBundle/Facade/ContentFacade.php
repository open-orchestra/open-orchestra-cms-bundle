<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class ContentFacade
 */
class ContentFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("integer")
     */
    public $contentId;

    /**
     * @Serializer\Type("string")
     */
    public $contentType;

    /**
     * @Serializer\Type("string")
     */
    public $siteId;

    /**
     * @Serializer\Type("string")
     */
    public $name;

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
     * @Serializer\Type("PHPOrchestra\ApiBundle\Facade\StatusFacade")
     */
    public $status;

    /**
     * @Serializer\Type("array<PHPOrchestra\ApiBundle\Facade\ContentAttributeFacade>")
     */
    protected $attributes = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addAttribute(FacadeInterface $facade)
    {
        $this->attributes[] = $facade;
    }
}
