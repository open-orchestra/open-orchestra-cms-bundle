<?php

namespace PHPOrchestra\BackofficeBundle\Manager;

use PHPOrchestra\ModelBundle\Model\AreaContainerInterface;
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
     * @param Collection $areas
     * @param string $areaId
     */
    public function deleteAreaFromAreas(AreaContainerInterface $areaContainer, $areaId)
    {
        $areaContainer->removeAreaByAreaId($areaId);

        return $areaContainer;
    }
}
