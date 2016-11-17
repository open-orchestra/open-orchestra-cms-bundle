<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use OpenOrchestra\BaseApi\Model\ApiClientInterface;

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
