<?php

namespace OpenOrchestra\Backoffice\Security;

/**
 * Interface ContributionRoleInterface
 *
 * This interface is never implemented
 * It defines roles available on the platform
 */
interface ContributionRoleInterface
{
    const DEVELOPER               = 'ROLE_DEVELOPER';                    // Can manage the entire platform
    const PLATFORM_ADMIN          = 'ROLE_PLATFORM_ADMIN';               // Can manage the entire platform, dev parts excluded
    const SITE_ADMIN              = 'ROLE_SITE_ADMIN';                   // Can manage users, groups and site on a specific site

    const NODE_CONTRIBUTOR        = 'EDITORIAL_NODE_CONTRIBUTOR';        // Can create nodes, edit & delete own nodes
    const NODE_SUPER_EDITOR       = 'EDITORIAL_NODE_SUPER_EDITOR';       // Can edit someone else's node
    const NODE_SUPER_SUPRESSOR    = 'EDITORIAL_NODE_SUPER_SUPRESSOR';    // Can remove someone else's node

    const CONTENT_CONTRIBUTOR     = 'EDITORIAL_CONTENT_CONTRIBUTOR';     // Can create contents, edit & delete own contents
    const CONTENT_SUPER_EDITOR    = 'EDITORIAL_CONTENT_SUPER_EDITOR';    // Can edit someone else's content
    const CONTENT_SUPER_SUPRESSOR = 'EDITORIAL_CONTENT_SUPER_SUPRESSOR'; // Can remove someone else's content

    const TRASH_RESTORER          = 'EDITORIAL_TRASH_RESTORER';          // Can restore trash items
    const TRASH_SUPRESSOR         = 'EDITORIAL_TRASH_SUPRESSOR';         // Can purge trash items
}
