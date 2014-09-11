<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class TemplateFacade
 */
class TemplateFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("integer")
     */
    public $siteId;

    /**
     * @Serializer\Type("string")
     */
    public $templateId;

    /**
     * @Serializer\Type("string")
     */
    public $name;

    /**
     * @Serializer\Type("boolean")
     */
    public $deleted;

    /**
     * @Serializer\Type("string")
     */
    public $language;

    /**
     * @Serializer\Type("PHPOrchestra\ApiBundle\Facade\StatusFacade")
     */
    public $status;

    /**
     * @Serializer\Type("string")
     */
    public $boDirection;

    /**
     * @Serializer\Type("array<PHPOrchestra\ApiBundle\Facade\AreaFacade>")
     */
    protected $areas = array();

    /**
     * @Serializer\Type("array<PHPOrchestra\ApiBundle\Facade\BlockFacade>")
     */
    protected $blocks = array();

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
}
