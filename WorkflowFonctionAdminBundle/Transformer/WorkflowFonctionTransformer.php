<?php

namespace OpenOrchestra\WorkflowFonctionAdminBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\WorkflowFonctionAdminBundle\Facade\WorkflowFonctionFacade;
use OpenOrchestra\WorkflowFonction\Model\WorkflowFonctionInterface;

/**
 * Class WorkflowFonctionTransformer
 */
class WorkflowFonctionTransformer extends AbstractTransformer
{
    /**
     * @param WorkflowFonctionInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new WorkflowFonctionFacade();

        $facade->id = $mixed->getId();
        $facade->name = $mixed->getName();

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_workflowfonction_show',
            array('workflowFonctionId' => $mixed->getId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'open_orchestra_api_workflowfonction_delete',
            array('WorkflowFonctionId' => $mixed->getId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'open_orchestra_backoffice_workflowfonction_form',
            array('WorkflowFonctionId' => $mixed->getId())
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'workflowfonction';
    }
}
