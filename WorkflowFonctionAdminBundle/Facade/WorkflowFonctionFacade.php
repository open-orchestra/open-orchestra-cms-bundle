<?php

namespace OpenOrchestra\WorkflowFonctionAdminBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

/**
 * Class WorkflowFonctionFacade
 */
class WorkflowFonctionFacade extends AbstractFacade
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
