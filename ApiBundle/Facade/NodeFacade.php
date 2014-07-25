<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class NodeFacade
 */
class NodeFacade implements FacadeInterface
{
    protected $areas = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addAreas(FacadeInterface $facade)
    {
        $this->areas[] = $facade;
    }
}
