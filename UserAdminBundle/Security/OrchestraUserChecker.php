<?php

namespace OpenOrchestra\UserAdminBundle\Security;

use OpenOrchestra\UserAdminBundle\Exception\NoRolesException;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\User\UserInterface;
use OpenOrchestra\UserBundle\Model\UserInterface as OrchestraUserInterface;

/**
 * Class OrchestraUserChecker
 */
class OrchestraUserChecker extends UserChecker
{
    /**
     * {@inheritdoc}
     */
    public function checkPreAuth(UserInterface $user)
    {
        parent::checkPreAuth($user);

        if (!$user instanceof OrchestraUserInterface) {
            return;
        }

        $roles = $user->getRoles();
        if (
            empty($roles) ||
            (1 === count($roles) && $user->hasRole(OrchestraUserInterface::ROLE_DEFAULT))
        ) {
            $ex = new NoRolesException('Account has not access');
            $ex->setUser($user);
            throw $ex;
        }
    }
}
