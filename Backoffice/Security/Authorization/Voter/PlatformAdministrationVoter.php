<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class PlatformAdministrationVoter
 *
 * Voter checking rights on platform management
 */
class PlatformAdministrationVoter extends AbstractVoter
{
    /**
     * @return array
     */
    protected function getSupportedClasses()
    {
        array(
            'OpenOrchestra\ModelInterface\Model\KeywordInterface',
            'OpenOrchestra\BaseApi\Model\ApiClientInterface'
        );
    }

    /**
     * Only SuperAdmin (Dev & Plaform admin) can manage Keywords & Api clients
     *
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }
    }
}
