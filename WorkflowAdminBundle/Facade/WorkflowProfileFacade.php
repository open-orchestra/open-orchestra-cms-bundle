<?php

namespace OpenOrchestra\WorkflowAdminBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

/**
 * Class WorkflowProfileFacade
 */
class WorkflowProfileFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $label;

    /**
     * @Serializer\Type("string")
     */
    public $description;
}
