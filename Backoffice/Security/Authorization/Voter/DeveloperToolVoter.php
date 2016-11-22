<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;

/**
 * Class DeveloperToolVoter
 *
 * Voter checking rights on developer tools management
 */
class DeveloperToolVoter extends AbstractVoter
{
    /**
     * Return the list of supported classes
     *
     * @return array
     */
    protected function getSupportedClasses()
    {
        return array(
            'OpenOrchestra\ModelInterface\Model\ContentTypeInterface',
            'OpenOrchestra\WorkflowFunction\Model\WorkflowProfileInterface',
            'OpenOrchestra\ModelInterface\Model\RoleInterface',
            'OpenOrchestra\ModelInterface\Model\StatusInterface'
        );
    }

    /**
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        return $token->getUser()->hasRole(ContributionRoleInterface::DEVELOPER);
    }
}
