<?php

namespace OpenOrchestra\Backoffice\Security;

/**
 * Interface ContributionRoleInterface
 */
interface ContributionRoleInterface
{
    const DEVELOPER      = 'ROLE_DEVELOPER';
    const PLATFORM_ADMIN = 'ROLE_PLATFORM_ADMIN';

    const NODE_SELF      = 'EDITORIAL_NODE_SELF';
    const NODE_EDIT      = 'EDITORIAL_NODE_EDIT';
    const NODE_DELETE    = 'EDITORIAL_NODE_DELETE';
}
