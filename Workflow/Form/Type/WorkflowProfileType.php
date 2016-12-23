<?php

namespace OpenOrchestra\Workflow\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class WorkflowProfileType
 */
class WorkflowProfileType extends AbstractType
{
    protected $workflowProfileClass;
    protected $backOfficeLanguages;

    /**
     * @param string $workflowProfileClass
     * @param array  $backOfficeLanguages
     */
    public function __construct($workflowProfileClass, array $backOfficeLanguages)
    {
        $this->backOfficeLanguages = $backOfficeLanguages;
        $this->workflowProfileClass = $workflowProfileClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('labels', 'oo_multi_languages', array(
                'label' => 'open_orchestra_workflow_admin.form.workflow_profile.labels',
                'languages' => $this->backOfficeLanguages,
                'group_id' => 'properties',
            ))
            ->add('descriptions', 'oo_multi_languages', array(
                'label' => 'open_orchestra_workflow_admin.form.workflow_profile.descriptions',
                'languages' => $this->backOfficeLanguages,
                'group_id' => 'properties',
            ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_workflow_profile';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'    => $this->workflowProfileClass,
            'group_enabled' => true,
            'group_render'  => array(
                'properties' => array(
                    'rank'  => 0,
                    'label' => 'open_orchestra_workflow_admin.form.workflow_profile.group.properties',
                ),
            ),
        ));
    }
}