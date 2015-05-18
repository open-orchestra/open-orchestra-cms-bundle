<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

/**
 * Class WorkflowFunctionFacade
 */
class WorkflowFunctionFacade extends AbstractFacade
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
