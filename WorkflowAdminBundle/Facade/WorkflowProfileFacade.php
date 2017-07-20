<?php

namespace OpenOrchestra\WorkflowAdminBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;

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

    /**
     * @Serializer\Type("array<OpenOrchestra\WorkflowAdminBundle\Facade\WorkflowTransitionFacade>")
     */
    public $transitions = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addTransition(FacadeInterface $facade)
    {
        $this->transitions[] = $facade;
    }
}
