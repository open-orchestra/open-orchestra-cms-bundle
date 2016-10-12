<?php

namespace OpenOrchestra\ApiBundle\Facade;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

/**
 * Class TemplateFacade
 */
class TemplateFacade extends AbstractFacade
{
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
    public $name;

    /**
     * @Serializer\Type("boolean")
     */
    public $deleted;

    /**
     * @Serializer\Type("string")
     * @deprecated will be removed in 2.0
     */
    public $language;

    /**
     * @Serializer\Type("string")
     * @deprecated will be removed in 2.0
     */
    public $boDirection;

    /**
     * @Serializer\Type("OpenOrchestra\ApiBundle\Facade\AreaFacade")
     */
    public $rootArea;

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\AreaFacade>")
     * @deprecated will be removed in 2.0
     */
    protected $areas;

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\BlockFacade>")
     */
    protected $blocks;

    /**
     * @Serializer\Type("boolean")
     */
    public $editable;

    /**
     * @param FacadeInterface $facade
     * @deprecated will be removed in 2.0
     */
    public function addArea(FacadeInterface $facade)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);

        $this->areas[$facade->areaId] = $facade;
    }

    /**
     * @return array
     * @deprecated will be removed in 2.0
     */
    public function getAreas()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);

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
