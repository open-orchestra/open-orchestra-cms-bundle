<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;

/**
 * Class OrchestraRoleChoiceType
 */
class OrchestraRoleChoiceType extends AbstractType
{
    protected $roleRepository;
    protected $translationChoiceManager;

    /**
     * @param RoleRepositoryInterface  $roleRepository
     * @param TranslationChoiceManager $translationChoiceManager
     */
    public function __construct(RoleRepositoryInterface $roleRepository, TranslationChoiceManager $translationChoiceManager)
    {
        $this->roleRepository = $roleRepository;
        $this->translationChoiceManager = $translationChoiceManager;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
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

        foreach ($this->roleRepository->findAll() as $role) {
            $choices[$role->getName()] = $this->translationChoiceManager->choose($role->getDescriptions());
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
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent()
    {
        return 'choice';
    }
}
