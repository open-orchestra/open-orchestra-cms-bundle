<?php

namespace OpenOrchestra\Backoffice\Manager;

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
     * @param  AreaFlexInterface $areaParent
     *
     * @return AreaFlexInterface
     */
    public function initializeNewAreaRow(AreaFlexInterface $areaParent)
    {
        $area = $this->initializeNewArea(AreaFlexInterface::TYPE_ROW);

        $lastAreaId = $this->getChildLastId($areaParent) + 1;
        $areaId = $areaParent->getAreaId().'_'.AreaFlexInterface::TYPE_ROW.'_'.$lastAreaId;
        $area->setAreaId($areaId);

        return $area;
    }

    /**
     * @param  AreaFlexInterface $areaParent
     *
     * @return AreaFlexInterface
     */
    public function initializeNewAreaColumn(AreaFlexInterface $areaParent)
    {
        $area = $this->initializeNewArea(AreaFlexInterface::TYPE_COLUMN);

        $lastAreaId = $this->getChildLastId($areaParent) + 1;
        $areaId = $areaParent->getAreaId().'_'.AreaFlexInterface::TYPE_COLUMN.'_'.$lastAreaId;
        $area->setAreaId($areaId);

        return $area;
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
     * @param AreaFlexInterface $area
     *
     * @return int
     */
    protected function getChildLastId(AreaFlexInterface $area)
    {
        $id = 0;
        foreach ($area->getAreas() as $subArea) {
            $areaIdExplode = explode('_', $subArea->getAreaId());
            $idSubArea = (int) end($areaIdExplode);
            if ($idSubArea > $id) {
                $id = $idSubArea;
            }
        }

        return $id;
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
