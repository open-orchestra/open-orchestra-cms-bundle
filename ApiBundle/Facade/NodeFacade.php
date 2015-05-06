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
    public $templateId;

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
    public $metaKeywords;

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
     * @Serializer\Type("OpenOrchestra\ApiBundle\Facade\StatusFacade")
     */
    public $status;

    /**
     * @Serializer\Type("string")
     */
    public $statusId;

    /**
     * @Serializer\Type("string")
     */
    public $theme;

    /**
     * @Serializer\Type("integer")
     */
    public $version;

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\AreaFacade>")
     */
    protected $areas = array();

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\BlockFacade>")
     */
    protected $blocks = array();

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\LinkFacade>")
     */
    protected $previewLinks = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addArea(FacadeInterface $facade)
    {
        $this->areas[] = $facade;
    }

    /**
     * @return array
     */
    public function getAreas()
    {
        return $this->areas;
    }

    /**
     * @param FacadeInterface $facade
     */
    public function addBlock(FacadeInterface $facade)
    {
        $this->blocks[] = $facade;
    }
    /**
     * @return array
     */
    public function getBlocks()
    {
        return $this->blocks;
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
