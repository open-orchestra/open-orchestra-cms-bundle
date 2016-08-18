<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        $builder->add('name', null, array(
            'label' => 'open_orchestra_backoffice.form.status.name'
        ))
        ->add('published', null, array(
            'required' => false,
            'label' => 'open_orchestra_backoffice.form.status.published'
        ))
        ->add('blockedEdition', 'checkbox', array(
            'label' => 'open_orchestra_backoffice.form.status.blocked_edition',
            'required' => false,
        ))
        ->add('initial', null, array(
            'required' => false,
            'label' => 'open_orchestra_backoffice.form.status.initial'
        ))
        ->add('autoPublishFrom', null, array(
            'required' => false,
            'label' => 'open_orchestra_backoffice.form.status.auto_publish_from'
        ))
        ->add('autoUnpublishTo', null, array(
            'required' => false,
            'label' => 'open_orchestra_backoffice.form.status.auto_unpublish_to'
        ))
        ->add('labels', 'oo_multi_languages', array(
            'label' => 'open_orchestra_backoffice.form.status.labels',
            'languages' => $this->backOfficeLanguages
        ))
        ->add('displayColor', 'orchestra_color_choice', array(
            'label' => 'open_orchestra_backoffice.form.status.display_color'
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
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->statusClass
        ));
    }

}
