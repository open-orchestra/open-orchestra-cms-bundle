<?php

namespace OpenOrchestra\Backoffice\Security;

/**
 * Interface ContributionAction
 *
 * This interface is never implemented
 * It defines actions available on the platform
 */
interface ContributionActionInterface
{
    const READ   = 'ACTION_READ';
    const ADD    = 'ACTION_ADD';
    const EDIT   = 'ACTION_EDIT';
    const DELETE = 'ACTION_DELETE';
}
