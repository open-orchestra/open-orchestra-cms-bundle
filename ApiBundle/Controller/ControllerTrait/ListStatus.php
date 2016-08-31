<?php

namespace OpenOrchestra\ApiBundle\Controller\ControllerTrait;

use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Model\IsStatusableInterface;

/**
 * Trait ListStatus
 */
trait ListStatus
{
    /**
     * @param StatusableInterface $document
     *
     * @return \OpenOrchestra\ApiBundle\Facade\StatusCollectionFacade
     */
    protected function listStatuses(StatusableInterface $document)
    {
        if (!$document instanceof IsStatusableInterface || $document->isStatusable()) {

            $transitions = $document->getStatus()->getFromRoles();

            $possibleStatuses = array();

            foreach ($transitions as $transition) {
                $possibleStatuses[] = $transition->getToStatus();
            }

            return $this->get('open_orchestra_api.transformer_manager')->get('status_collection')->transform($possibleStatuses, $document);
        }

        return array();
    }
}
