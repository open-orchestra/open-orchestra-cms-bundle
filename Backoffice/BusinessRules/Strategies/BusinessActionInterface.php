<?php

namespace OpenOrchestra\Backoffice\BusinessRules\Strategies;

/**
 * Interface BusinessAction
 *
 * This interface is never implemented
 * It defines actions available on the platform
 */
interface BusinessActionInterface
{
    const READ          = 'ACTION_READ';
    const CREATE        = 'ACTION_CREATE';
    const EDIT          = 'ACTION_EDIT';
    const DELETE        = 'ACTION_DELETE';
}
