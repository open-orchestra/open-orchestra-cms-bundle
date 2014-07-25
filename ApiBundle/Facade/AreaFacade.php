<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class AreaFacade
 */
class AreaFacade implements FacadeInterface
{
    /**
     * @Serializer\Type("string")
     */
    public $areaId;

    /**
     * @Serializer\Type("string")
     */
    public $classes;

    /**
     * @Serializer\Type("array<PHPOrchestra\ApiBundle\Facade\BlockFacade>")
     */
    protected $blocks = array();

    /**
     * @Serializer\Type("array<PHPOrchestra\ApiBundle\Facade\AreaFacade>")
     */
    protected $areas = array();

    /**
     * @Serializer\Type("PHPOrchestra\ApiBundle\Facade\UiModelFacade")
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
     * @param FacadeInterface $facade
     */
    public function addArea(FacadeInterface $facade)
    {
        $this->areas[] = $facade;
    }
}
