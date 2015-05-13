<?php

namespace OpenOrchestra\WorkflowFonctionAdminBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\WorkflowFonctionAdminBundle\Facade\WorkflowFonctionCollectionFacade;

/**
 * Class WorkflowFonctionCollectionTransformer
 */
class WorkflowFonctionCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new WorkflowFonctionCollectionFacade();

        foreach ($mixed as $workflowFonction) {
            $facade->addWorkflowFonction($this->getTransformer('workflowfonction')->transform($workflowFonction));
        }

        $facade->addLink('_self_add', $this->generateRoute(
            'open_orchestra_backoffice_workflowfonction_new',
            array()
        ));

        $facade->addLink('_translate', $this->generateRoute(
            'open_orchestra_api_translate'
        ));

        return $facade;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return 'workflowfonction_collection';
    }
}
