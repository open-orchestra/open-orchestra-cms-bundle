<?php

namespace PHPOrchestra\BackofficeBundle\Manager;

use PHPOrchestra\ModelBundle\Document\Area;

/**
 * Class AreaManager
 */
class AreaManager
{
    /*
     * The document manager service
     */
    protected $documentManager;

    /**
     * Constructor
     */
    public function __construct($documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * Remove an area from an AreaCollections
     *
     * @param BaseCollection $areas
     * @param string $areaId
     */
    public function deleteAreaFromAreas($areas, $areaId)
    {
        foreach ($areas as $key => $area) {
            if ($areaId == $area->getAreaId()) {
                $this->documentManager->remove($area);
                unset($areas[$key]);
                break;
            }
        }

        return $areas;
    }
}
