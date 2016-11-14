<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter as BaseAbstractVoter;

/**
 * Class AbstractVoter
 */
abstract class AbstractVoter extends BaseAbstractVoter
{
    /**
     * @param UserInterface|string $user
     *
     * @return bool
     */
    protected function isSuperAdmin($user = null)
    {
        return ($user instanceof UserInterface && $user->isSuperAdmin());
    }
}
