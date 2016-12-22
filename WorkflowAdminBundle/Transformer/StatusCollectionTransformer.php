<?php

namespace OpenOrchestra\WorkflowAdminBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;

/**
 * Class StatusCollectionTransformer
 */
class StatusCollectionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param Collection               $statusCollection
     *
     * @return FacadeInterface
     */
    public function transform($statusCollection)
    {
        $facade = $this->newFacade();

        foreach ($statusCollection as $status) {
            $facade->addStatus($this->getTransformer('status')->transform($status));
        }

        $facade->addRight(
            'can_create',
            $this->authorizationChecker->isGranted(ContributionActionInterface::CREATE, StatusInterface::ENTITY_TYPE)
        );

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'status_collection';
    }

}
