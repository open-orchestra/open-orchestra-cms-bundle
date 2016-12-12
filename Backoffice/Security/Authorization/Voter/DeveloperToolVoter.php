<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Model\RoleInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Model\WorkflowProfileInterface;
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
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supportSubject($subject)
    {
        if (is_object($subject)) {
            return $this->supportClasses(
                $subject,
                array(
                    'OpenOrchestra\ModelInterface\Model\ContentTypeInterface',
                    'OpenOrchestra\ModelInterface\Model\WorkflowProfileInterface',
                    'OpenOrchestra\ModelInterface\Model\RoleInterface',
                    'OpenOrchestra\ModelInterface\Model\StatusInterface'
                )
            );
        }

        return in_array(
            $subject,
            array(
                ContentTypeInterface::ENTITY_TYPE,
                WorkflowProfileInterface::ENTITY_TYPE,
                RoleInterface::ENTITY_TYPE,
                StatusInterface::ENTITY_TYPE
            )
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
