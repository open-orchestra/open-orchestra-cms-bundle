<?php

namespace OpenOrchestra\Backoffice\AuthorizeStatusChange;

use OpenOrchestra\ModelInterface\Event\StatusableEvent;

/**
 * Interface AuthorizeStatusChangeInterface
 */
interface AuthorizeStatusChangeInterface
{
    /**
     * @param StatusableEvent $event
     *
     * @return bool
     */
    public function isGranted(StatusableEvent $statusableEvent);

    /**
     * @return string
     */
    public function getName();
}
