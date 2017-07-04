<?php

namespace OpenOrchestra\WorkflowAdminBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Model\WorkflowProfileInterface;

/**
 * Class WorkflowProfileCollectionTransformer
 */
class WorkflowProfileCollectionTransformer extends AbstractTransformer
{
    /**
     * @param Collection $workflowProfileCollection
     * @param array      $params
     *
     * @return FacadeInterface
     */
    public function transform($workflowProfileCollection, array $params = array())
    {
        $facade = $this->newFacade();

        foreach ($workflowProfileCollection as $workflowProfile) {
            $facade->addWorkflowProfile($this->getContext()->transform('workflow_profile', $workflowProfile));
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param array           $params
     *
     * @return WorkflowProfileInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, array $params = array())
    {
        $workflowProfiles = array();
        $workflowProfilesFacade = $facade->getWorkflowProfiles();
        foreach ($workflowProfilesFacade as $workflowProfileFacade) {
            $workflowProfile = $this->getContext()->reverseTransform('workflow_profile', $workflowProfileFacade);
            if (null !== $workflowProfile) {
                $workflowProfiles[] = $workflowProfile;
            }
        }

        return $workflowProfiles;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'workflow_profile_collection';
    }

}
