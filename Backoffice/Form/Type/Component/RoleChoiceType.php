<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use OpenOrchestra\Backoffice\Collector\RoleCollectorInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * Class RoleChoiceType
 */
class RoleChoiceType extends AbstractType
{
    protected $roleCollectors;
    protected $name;
    protected $rolesClassification;

    /**
     * @param array<RoleCollectorInterface> $roleCollectors
     * @param string                        $name
     * @param array                         $rolesClassification
     */
    public function __construct(array $roleCollectors, $name, array $rolesClassification = array())
    {
        $this->roleCollectors = $roleCollectors;
        $this->name = $name;
        $this->rolesClassification = $rolesClassification;
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $rolesOrdered = array();
        $choices = $this->getRolesInRoleCollectors();
        foreach($this->rolesClassification as $key => $classification) {
            if(($rank = array_search(strtoupper($key), array_keys($choices))) !== false) {
                $rolesOrdered[$classification['category']][$classification['label']][] = $rank;
            }
        }
        $view->vars['rolesOrdered'] = $rolesOrdered;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->getRolesInRoleCollectors(),
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * @return array
     */
    protected function getRolesInRoleCollectors()
    {
        $roles = array();
        foreach ($this->roleCollectors as $roleCollector) {
            $roles = array_merge($roles, $roleCollector->getRoles());
        }

        return $roles;
    }
}
