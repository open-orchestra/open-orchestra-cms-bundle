<?php

namespace OpenOrchestra\UserAdminBundle\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * Class NoRolesException
 */
class NoRolesException extends AccountStatusException
{
    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        return 'open_orchestra_user_admin.security.no_roles';
    }
}
