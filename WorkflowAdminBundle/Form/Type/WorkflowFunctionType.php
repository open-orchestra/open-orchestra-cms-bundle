<?php

namespace OpenOrchestra\WorkflowAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class WorkflowFunctionType
 */
class WorkflowFunctionType extends AbstractType
{
    protected $workflowFunctionClass;
    protected $backOfficeLanguages;

    /**
     * @param string $workflowFunctionClass
     * @param array  $backOfficeLanguages
     */
    public function __construct(
        $workflowFunctionClass,
        array $backOfficeLanguages
    )
    {
        $this->backOfficeLanguages = $backOfficeLanguages;
        $this->workflowFunctionClass = $workflowFunctionClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('names', 'oo_multi_languages', array(
                'label' => 'open_orchestra_workflow_admin.form.workflow_function.name',
                'languages' => $this->backOfficeLanguages
            ))
            ->add('roles', 'oo_workflow_role_choice', array(
                'label' => 'open_orchestra_workflow_admin.form.workflow_function.role',
                'multiple' => true,
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
        return 'oo_workflow_function';
    }
}
