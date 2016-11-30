<?php

namespace OpenOrchestra\WorkflowAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            'type' => 'oo_authorization',
            'label' => false,
            'required' => false,
        ));

        if (array_key_exists('disabled', $options)) {
            $builder->setAttribute('disabled', $options['disabled']);
        }
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
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
        return 'oo_workflow_right';
    }

}
