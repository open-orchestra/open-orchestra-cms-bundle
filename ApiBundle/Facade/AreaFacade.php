<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class AreaFacade
 */
class AreaFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $label;

    /**
     * @Serializer\Type("string")
     */
    public $areaId;

    /**
     * @Serializer\Type("string")
     */
    public $classes;

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\BlockFacade>")
     */
    protected $blocks = array();

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\AreaFacade>")
     */
    protected $areas = array();

    /**
     * @Serializer\Type("string")
     */
    public $boDirection;

    /**
     * @Serializer\Type("OpenOrchestra\ApiBundle\Facade\UiModelFacade")
     */
    public $uiModel;

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
}
