<?php

namespace OpenOrchestra\Backoffice\AuthorizeEdition;

use OpenOrchestra\ModelInterface\Model\StatusableInterface;

/**
 * Class StatusableStrategy
 */
class StatusableStrategy implements AuthorizeEditionInterface
{
    /**
     * @param mixed $document
     *
     * @return bool
     */
    public function support($document)
    {
        return $document instanceof StatusableInterface;
    }

    /**
     * @param StatusableInterface|mixed $document
     *
     * @return bool
     */
    public function isEditable($document)
    {
        return !$document->getStatus()->isPublished();
    }
}
