<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter as BaseAbstractVoter;

/**
 * Class AbstractVoter
 */
abstract class AbstractVoter extends BaseAbstractVoter
{
    /**
     * If you have a simple voter triggering on certain classes and certain attributes,
     * only override the getSupportedClasses and getSupportedAttributes methods.
     *
     * If you have a more complex supports mixing both attribute and subject,
     * then overide this method
     */
    protected function supports($attribute, $subject)
    {
        $attributeSupported = in_array($attribute, $this->getSupportedAttributes());

        $classSupported = false;
        foreach ($this->getSupportedClasses() as $supportedClass) {
            if ($subject instanceof $supportedClass) {
                $classSupported = true;
                break;
            }
      }

        return $attribute && $classSupported;
    }

    /**
     * If you have a simple voter triggering on certain classes and certain attributes,
     * you can simply override this method to return the list of supported classes
     *
     * @return array
     */
    protected function getSupportedClasses()
    {
        return array();
    }

    /**
     * If you have a simple voter triggering on certain classes and certain attributes,
     * you can simply ovveride this method to return the list of supported attributes
     *
     * @return array
     */
    protected function getSupportedAttributes()
    {
        return array();
    }

    /**
     * @param UserInterface|string $user
     *
     * @return bool
     */
    protected function isSuperAdmin($user = null)
    {
        return ($user instanceof UserInterface
            && ($user->hasRole('ROLE_DEVELOPPER') || $user->hasRole('ROLE_PLATFORM_ADMIN'))
        );
    }
}
