<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class WorkflowFunctionType
 */
class WorkflowFunctionType extends AbstractType
{
    protected $workflowFunctionClass;

    /**
     * @param string $workflowFunctionClass
     */
    public function __construct($workflowFunctionClass)
    {
        $this->workflowFunctionClass = $workflowFunctionClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'open_orchestra_workflow_function_admin.form.workflow_function.name'
            ))
            ->add('roles', 'document', array(
                'class' => 'OpenOrchestra\ModelBundle\Document\Role',
                'property' => 'name',
                'label' => 'open_orchestra_workflow_function_admin.form.workflow_function.role',
                'multiple' => true
            ));
        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->workflowFunctionClass,
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'workflow_function';
    }
}
