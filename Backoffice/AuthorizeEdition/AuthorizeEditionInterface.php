<?php

namespace OpenOrchestra\Backoffice\AuthorizeEdition;

/**
 * Interface AuthorizeEditionInterface
 */
interface AuthorizeEditionInterface
{
    /**
     * @param mixed $document
     *
     * @return bool
     */
    public function support($document);

    /**
     * @param mixed $document
     *
     * @return bool
     */
    public function isEditable($document);
}
