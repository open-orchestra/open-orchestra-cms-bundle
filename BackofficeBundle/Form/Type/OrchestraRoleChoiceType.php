<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\Backoffice\Collector\RoleCollector;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class OrchestraRoleChoiceType
 */
class OrchestraRoleChoiceType extends AbstractType
{
    protected $roleCollector;
    protected $translator;

    /**
     * @param RoleCollector       $roleCollector
     * @param TranslatorInterface $translator
     */
    public function __construct(RoleCollector $roleCollector, TranslatorInterface $translator)
    {
        $this->roleCollector = $roleCollector;
        $this->translator = $translator;
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

        foreach ($this->roleCollector->getRoles() as $role) {
            $choices[$role] = $this->translator->trans('open_orchestra_role.' . strtolower($role));
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
