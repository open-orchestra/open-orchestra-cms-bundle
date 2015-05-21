<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class WorkflowRightType
 */
class WorkflowRightType extends AbstractType
{
    protected $workflowRightClass;

    /**
     * @param string $workflowRightClass
     */
    public function __construct($workflowRightClass)
    {
        $this->workflowRightClass = $workflowRightClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('authorizations', 'collection', array(
            'type' => 'authorization',
            'label' => false,
            'required' => false,
        ));

        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->workflowRightClass
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'workflow_right';
    }

}
