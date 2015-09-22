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
     * @return StatusCollectionFacade
     */
    protected function listStatuses(StatusableInterface $document)
    {
        $transitions = $document->getStatus()->getFromRoles();

        $possibleStatuses = array();

        foreach ($transitions as $transition) {
            $possibleStatuses[] = $transition->getToStatus();
        }

        return $this->get('open_orchestra_api.transformer_manager')->get('status_collection')->transform($possibleStatuses, $document);
    }
}
