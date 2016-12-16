<?php

namespace OpenOrchestra\Backoffice\Collector;

use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class BackofficeRoleCollector
 */
class BackofficeRoleCollector implements RoleCollectorInterface
{
    protected $roles = array();
    protected $roleRepository;
    protected $translator;
    protected $multiLanguagesChoiceManager;

    /**
     * @param TranslatorInterface                  $translator
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     */
    public function __construct(TranslatorInterface $translator, MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager)
    {
        $this->translator = $translator;
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param string $role
     */
    public function addRole($role)
    {
        $this->addRoleWithTranslation($role, $this->translator->trans('open_orchestra_role.' . strtolower($role), array(), 'role'));
    }

    /**
     * @param string $role
     * @param string $translation
     */
    protected function addRoleWithTranslation($role, $translation)
    {
        $this->roles[$role] = $translation;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function getRolesByType($type)
    {
        $selectedRoles = array();

        foreach ($this->roles as $role => $translation) {
            if (preg_match('/^role_access_([^_]*_)?' . $type . '$/i', $role)) {
                $selectedRoles[$role] = $translation;
            }
        }

        return $selectedRoles;
    }

    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        return array_key_exists($role, $this->roles);
    }
}
