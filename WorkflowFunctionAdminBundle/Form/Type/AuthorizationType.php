<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AuthorizationType
 */
class AuthorizationType extends AbstractType
{
    protected $authorizationClass;

    /**
     * @param string $authorizationClass
     */
    public function __construct($authorizationClass)
    {
        $this->authorizationClass = $authorizationClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'hidden');
        $builder->add('workflowFunctions', 'orchestra_workflow_function', array(
            'label' => false,
            'required' => false
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['label'] = $view->vars['value']->getName();
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => $this->authorizationClass,
            )
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'authorization';
    }

}
