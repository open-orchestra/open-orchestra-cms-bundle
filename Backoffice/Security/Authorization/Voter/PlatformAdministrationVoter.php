<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use OpenOrchestra\BaseApi\Model\ApiClientInterface;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class PlatformAdministrationVoter
 *
 * Voter checking rights on platform management
 */
class PlatformAdministrationVoter extends AbstractVoter
{
    /**
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supportSubject($subject)
    {
        if (is_object($subject)) {
            return $this->supportedClasses(
                $subject,
                array(
                    'OpenOrchestra\ModelInterface\Model\KeywordInterface',
                    'OpenOrchestra\BaseApi\Model\ApiClientInterface'
                )
            );
        }

        return in_array(
            $subject,
            array(
                KeywordInterface::ENTITY_TYPE,
                ApiClientInterface::ENTITY_TYPE
            )
        );
    }

    /**
     * Only SuperAdmin (Dev & Platform admin) can manage Keywords & Api clients
     *
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->isSuperAdmin($token->getUser())) {
            return true;
        }

        return false;
    }
}
