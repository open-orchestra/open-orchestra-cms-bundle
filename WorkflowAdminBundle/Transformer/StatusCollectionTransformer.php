<?php

namespace OpenOrchestra\WorkflowAdminBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;

/**
 * Class StatusCollectionTransformer
 */
class StatusCollectionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param Collection               $statusCollection
     * @param StatusableInterface|null $document
     *
     * @return FacadeInterface
     */
    public function transform($statusCollection, $document = null)
    {
        $facade = $this->newFacade();

        foreach ($statusCollection as $status) {
            $facade->addStatus($this->getTransformer('status')->transform($status, $document));
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
