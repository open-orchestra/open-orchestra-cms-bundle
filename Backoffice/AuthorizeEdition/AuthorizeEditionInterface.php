<?php

namespace OpenOrchestra\Backoffice\AuthorizeEdition;

/**
 * Interface AuthorizeEditionInterface
 *
 * @deprecated use the AuthorizationChecker instead, will be removed in 1.2.0
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
