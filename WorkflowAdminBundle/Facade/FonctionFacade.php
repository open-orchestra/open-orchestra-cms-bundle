<?php

namespace OpenOrchestra\WorkflowAdminBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

/**
 * Class FonctionFacade
 */
class FonctionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $id;

    /**
     * @Serializer\Type("string")
     */
    public $name;

}
