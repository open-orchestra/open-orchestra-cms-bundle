<?php

namespace OpenOrchestra\Workflow\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * Class StatusType
 */
class StatusType extends AbstractType
{
    protected $statusClass;
    protected $backOfficeLanguages;

    /**
     * @param string $statusClass
     * @param array  $backOfficeLanguages
     */
    public function __construct($statusClass, array $backOfficeLanguages)
    {
        $this->backOfficeLanguages = $backOfficeLanguages;
        $this->statusClass = $statusClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label'    => 'open_orchestra_workflow_admin.form.status.name',
                'group_id' => 'properties',
            ))
            ->add('labels', 'oo_multi_languages', array(
                'label'     => 'open_orchestra_workflow_admin.form.status.labels',
                'languages' => $this->backOfficeLanguages,
                'group_id'  => 'properties',
            ))
            ->add('displayColor', 'orchestra_color_choice', array(
                'label'    => 'open_orchestra_workflow_admin.form.status.display_color',
                'group_id' => 'properties',
            ))
            ->add('properties', 'text', array(
                'mapped'   => false,
                'required' => false,
                'disabled' => true,
                'label'    => 'open_orchestra_workflow_admin.form.status.properties.label',
                'group_id' => 'properties',
            ))
            ->add('blockedEdition', 'checkbox', array(
                'label'    => 'open_orchestra_workflow_admin.form.status.blocked_edition.label',
                'required' => false,
                'attr'     => array('help_text' => 'open_orchestra_workflow_admin.form.status.blocked_edition.helper'),
                'group_id' => 'properties',
            ));

        if (array_key_exists('disabled', $options)) {
            $builder->setAttribute('disabled', $options['disabled']);
        }
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_status';
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $status = $form->getData();
        $properties = array();

        if ($status->isInitialState()) {
            $properties[] = 'open_orchestra_workflow_admin.form.status.properties.initial_state';
        }
        if ($status->isTranslationState()) {
            $properties[] = 'open_orchestra_workflow_admin.form.status.properties.translation_state';
        }
        if ($status->isPublishedState()) {
            $properties[] = 'open_orchestra_workflow_admin.form.status.properties.published_state';
        }
        if ($status->isAutoPublishFromState()) {
            $properties[] = 'open_orchestra_workflow_admin.form.status.properties.auto_publish_from_state';
        }
        if ($status->isAutoUnpublishToState()) {
            $properties[] = 'open_orchestra_workflow_admin.form.status.properties.auto_unpublish_to_state';
        }
        $view->vars['properties'] = $properties;

        $view->vars['delete_button'] = $options['delete_button'];
        $view->vars['delete_business_rules'] = $options['delete_business_rules'];
        $view->vars['business_rules_help_text'] = $options['business_rules_help_text'];
        $view->vars['new_button'] = $options['new_button'];
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'    => $this->statusClass,
            'delete_button' => false,
            'delete_business_rules' => false,
            'business_rules_help_text' => 'open_orchestra_workflow_admin.form.status.business_rules_help_text',
            'new_button' => false,
            'group_enabled' => true,
            'group_render'  => array(
                'properties' => array(
                    'rank'  => 0,
                    'label' => 'open_orchestra_workflow_admin.form.status.group.properties',
                ),
            ),
        ));
    }

}
