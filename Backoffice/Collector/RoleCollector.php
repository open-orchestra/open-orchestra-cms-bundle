<?php

namespace OpenOrchestra\Backoffice\Collector;

use OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface;
use Symfony\Component\Translation\TranslatorInterface;
use OpenOrchestra\ModelInterface\Manager\TranslationChoiceManagerInterface;

/**
 * Class RoleCollector
 */
class RoleCollector
{
    protected $roles = array();
    protected $roleRepository;
    protected $translator;
    protected $translationChoiceManager;

    /**
     * @param RoleRepositoryInterface           $roleRepository
     * @param TranslatorInterface               $translator
     * @param TranslationChoiceManagerInterface $translationChoiceManager
     * @param boolean                           $workflowRoleInGroup
     */
    public function __construct(RoleRepositoryInterface $roleRepository, TranslatorInterface $translator, TranslationChoiceManagerInterface $translationChoiceManager, $workflowRoleInGroup)
    {
        $this->roleRepository = $roleRepository;
        $this->translator = $translator;
        $this->translationChoiceManager = $translationChoiceManager;
        if ($workflowRoleInGroup) {
            $this->loadWorkflowRole();
        }
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
     * add workflow roles from repository
     */
    protected function loadWorkflowRole()
    {
        $workflowRoles = $this->roleRepository->findWorkflowRole();
        foreach ($workflowRoles as $workflowRole) {
            $this->addRoleWithTranslation($workflowRole->getName(), $this->translationChoiceManager->choose($workflowRole->getDescriptions()));
        }
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
            if (preg_match('/role_access_([^_]*_)?' . $type . '$/i', $role)) {
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
