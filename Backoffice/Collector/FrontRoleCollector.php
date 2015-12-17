<?php

namespace OpenOrchestra\Backoffice\Collector;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class FrontRoleCollector
 */
class FrontRoleCollector implements RoleCollectorInterface
{
    protected $roles = array();
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        $this->roles[UserInterface::ROLE_DEFAULT] = $this->translator->trans(
            'open_orchestra_role.' . strtolower(UserInterface::ROLE_DEFAULT),
            array(),
            'role'
        );

        return $this->roles;
    }

    /**
     * @param string $role
     */
    public function addRole($role)
    {
        $this->roles[$role] = $this->translator->trans('open_orchestra_role.' . strtolower($role), array(), 'role');
    }

    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        return array_key_exists($role, $this->getRoles());
    }
}
