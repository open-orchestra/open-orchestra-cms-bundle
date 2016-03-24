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
    protected $roleCollector;
    protected $name;
    protected $parameters;

    /**
     * @param RoleCollectorInterface $roleCollector
     * @param string                 $name
     */
    public function __construct(RoleCollectorInterface $roleCollector, $name, $parameters)
    {
        $this->roleCollector = $roleCollector;
        $this->name = $name;
        $this->parameters = $parameters;
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $parameters = array();
        $choices = $this->roleCollector->getRoles();
        foreach($this->parameters as $key => $parameter) {
            $parameters[$parameter['category']][$parameter['label']][] = array_search(strtoupper($key), array_keys($choices));
        }
        $view->vars['parameters'] = $parameters;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->roleCollector->getRoles(),
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
}
