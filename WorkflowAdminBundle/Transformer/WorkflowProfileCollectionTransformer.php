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
     *
     * @return FacadeInterface
     */
    public function transform($workflowProfileCollection)
    {
        $facade = $this->newFacade();

        foreach ($workflowProfileCollection as $workflowProfile) {
            $facade->addWorkflowProfile($this->getTransformer('workflow_profile')->transform($workflowProfile));
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param null            $source
     *
     * @return WorkflowProfileInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        $workflowProfiles = array();
        $workflowProfilesFacade = $facade->getWorkflowProfiles();
        foreach ($workflowProfilesFacade as $workflowProfileFacade) {
            $workflowProfile = $this->getTransformer('workflow_profile')->reverseTransform($workflowProfileFacade);
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
