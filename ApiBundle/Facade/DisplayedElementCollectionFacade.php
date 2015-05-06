<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

/**
 * Class DisplayedElementCollectionFacade
 */
class DisplayedElementCollectionFacade extends AbstractFacade
{

    /**
     * @Serializer\Type("array<string>")
     */
    public $displayedElements = array();

}
