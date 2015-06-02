<?php

namespace OpenOrchestra\Backoffice\AuthorizeStatusChange;

use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;

/**
 * Interface AuthorizeStatusChangeInterface
 */
interface AuthorizeStatusChangeInterface
{
    /**
     * @param StatusableInterface $document
     * @param StatusInterface     $toStatus
     *
     * @return bool
     */
    public function isGranted(StatusableInterface $document, StatusInterface $toStatus);

    /**
     * @return string
     */
    public function getName();
}
