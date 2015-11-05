<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

/**
 * Class OrchestraRoleChoiceType
 */
class OrchestraRoleChoiceType extends AbstractType
{
    protected $roleRepository;
    protected $workflowRoleInGroup;
    protected $translationChoiceManager;

    /**
     * @param RoleRepositoryInterface  $roleRepository
     * @param TranslationChoiceManager $translationChoiceManager
     * @param bool|true                $workflowRoleInGroup
     */
    public function __construct(RoleRepositoryInterface $roleRepository, TranslationChoiceManager $translationChoiceManager, $workflowRoleInGroup = true)
    {
        $this->roleRepository = $roleRepository;
        $this->workflowRoleInGroup = $workflowRoleInGroup;
        $this->translationChoiceManager = $translationChoiceManager;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->getChoices()
        ));
    }

    /**
     * @return array
     */
    protected function getChoices()
    {
        $choices = array();

        foreach ($this->roleRepository->findAccessRole() as $role) {
            $choices[$role->getName()] = $this->translationChoiceManager->choose($role->getDescriptions());
        }

        if ($this->workflowRoleInGroup) {
            foreach ($this->roleRepository->findWorkflowRole() as $role) {
                $choices[$role->getName()] = $this->translationChoiceManager->choose($role->getDescriptions());
            }
        }

        return $choices;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'orchestra_role_choice';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'choice';
    }
}
