<?php

namespace OpenOrchestra\ApiBundle\Facade;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\Traits\BlameableFacade;

/**
 * Class NodeFacade
 */
class NodeFacade extends DeletedFacade
{
    use BlameableFacade;

    /**
     * @Serializer\Type("string")
     */
    public $nodeId;

    /**
     * @Serializer\Type("string")
     */
    public $siteId;

    /**
     * @Serializer\Type("string")
     */
    public $templateSet;

    /**
     * @Serializer\Type("string")
     */
    public $template;

    /**
     * @Serializer\Type("string")
     */
    public $nodeType;

    /**
     * @Serializer\Type("string")
     */
    public $parentId;

    /**
     * @Serializer\Type("string")
     */
    public $path;

    /**
     * @Serializer\Type("string")
     */
    public $routePattern;

    /**
     * @Serializer\Type("string")
     */
    public $language;

    /**
     * @Serializer\Type("string")
     */
    public $metaDescription;

    /**
     * @Serializer\Type("boolean")
     */
    public $metaIndex = false;

    /**
     * @Serializer\Type("boolean")
     */
    public $metaFollow = false;

    /**
     * @Serializer\Type("OpenOrchestra\WorkflowAdminBundle\Facade\StatusFacade")
     */
    public $status;

    /**
     * @Serializer\Type("string")
     */
    public $theme;

    /**
     * @Serializer\Type("boolean")
     */
    public $themeSiteDefault;

    /**
     * @Serializer\Type("integer")
     */
    public $version;

    /**
     * @Serializer\Type("integer")
     */
    public $order;

    /**
     * @Serializer\Type("boolean")
     */
    public $currentlyPublished;

    /**
     * @Serializer\Type("array<string,OpenOrchestra\ApiBundle\Facade\AreaFacade>")
     */
    protected $areas = array();

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\LinkFacade>")
     */
    protected $previewLinks;

    /**
     * @param FacadeInterface $facade
     */
    public function setAreas(FacadeInterface $facade, $key)
    {
        $this->areas[$key] = $facade;
    }
    /**
     * @return array
     */
    public function getAreas()
    {
        return $this->areas;
    }

    /**
     * @param FacadeInterface $previewLink
     */
    public function addPreviewLink(FacadeInterface $previewLink)
    {
        $this->previewLinks[] = $previewLink;
    }

    /**
     * @return array
     */
    public function getPreviewLinks()
    {
        return $this->previewLinks;
    }
}
