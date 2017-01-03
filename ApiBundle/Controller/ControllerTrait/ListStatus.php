<?php

namespace OpenOrchestra\ApiBundle\Controller\ControllerTrait;

use OpenOrchestra\ModelInterface\Model\StatusableInterface;

/**
 * Trait ListStatus
 */
trait ListStatus
{
    /**
     * @param StatusableInterface $document
     *
     * @return \OpenOrchestra\WorkflowAdminBundle\Facade\StatusCollectionFacade
     */
    protected function listStatuses(StatusableInterface $document)
    {
        $possibleStatuses = array($document->getStatus());
        $availableStatus = $this->get('open_orchestra_model.repository.status')->findAll();

        foreach ($availableStatus as $status) {
            if ($status->getId() != $document->getStatus()->getId()
                && $this->isGranted($status, $document)
            ) {
                $possibleStatuses[] = $status;
            }
        }

        return $this->get('open_orchestra_api.transformer_manager')->get('status_collection')->transform($possibleStatuses);
    }
}
