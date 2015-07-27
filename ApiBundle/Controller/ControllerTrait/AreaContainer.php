<?php

namespace OpenOrchestra\ApiBundle\Controller\ControllerTrait;

use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Model\AreaContainerInterface;

/**
 * Trait AreaContainer
 */
trait AreaContainer
{
    /**
     * Update an area from an areaContainer
     *
     * @param array                  $areas
     * @param AreaContainerInterface $areaContainer
     */
    protected function updateAreasFromContainer($areas, AreaContainerInterface $areaContainer)
    {
        $this->get('open_orchestra_backoffice.manager.area')->updateAreaFromContainer($areas, $areaContainer);
        $this->get('doctrine.odm.mongodb.document_manager')->flush();
    }
}
