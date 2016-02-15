<?php

namespace OpenOrchestra\BackofficeBundle\Manager;

use OpenOrchestra\ModelInterface\Model\AreaFlexInterface;

/**
 * Class AreaFlexManager
 */
class AreaFlexManager
{
    protected $areaFlexClass;

    /**
     * Constructor
     *
     * @param string $areaFlexClass
     */
    public function __construct($areaFlexClass)
    {
        $this->areaFlexClass = $areaFlexClass;
    }

    /**
     * @return AreaFlexInterface
     */
    public function initializeNewAreaRow()
    {
        return $this->initializeNewArea(AreaFlexInterface::TYPE_ROW);
    }

    /**
     * @return AreaFlexInterface
     */
    public function initializeNewAreaColumn()
    {
        return $this->initializeNewArea(AreaFlexInterface::TYPE_COLUMN);
    }

    /**
     * @return AreaFlexInterface
     */
    public function initializeNewAreaRoot()
    {
        /** @var AreaFlexInterface $area */
        $area = $this->initializeNewArea(AreaFlexInterface::TYPE_ROOT);
        $area->setAreaId(AreaFlexInterface::ROOT_AREA_ID);
        $area->setLabel(AreaFlexInterface::ROOT_AREA_LABEL);

        return $area;
    }

    /**
     * @param string $type
     *
     * @return AreaFlexInterface
     */
    protected function initializeNewArea($type)
    {
        /** @var AreaFlexInterface $area */
        $area = new $this->areaFlexClass();
        $area->setAreaType($type);

        return $area;
    }
}
