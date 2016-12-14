<?php

namespace OpenOrchestra\GroupBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class GroupType
 */
class GroupType extends AbstractType
{
    protected $groupClass;
    protected $backOfficeLanguages;

    /**
     * @param string $groupClass
     * @param array  $backOfficeLanguages
     */
    public function __construct(
        $groupClass,
        array $backOfficeLanguages
    ) {
        $this->groupClass = $groupClass;
        $this->backOfficeLanguages = $backOfficeLanguages;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => 'open_orchestra_group.form.group.name'
            ))
            ->add('labels', 'oo_multi_languages', array(
                'label' => 'open_orchestra_group.form.group.label',
                'languages' => $this->backOfficeLanguages
            ))
            ->add('site', 'oo_group_site_choice', array(
                'label' => 'open_orchestra_group.form.group.site',
                'required' => false,
            ))
            ->add('roles', 'oo_role_choice', array(
                'label' => 'open_orchestra_group.form.group.roles',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'attr' => array('help_text' => 'open_orchestra_group.form.group.role_helper'),
            ));

        if (array_key_exists('disabled', $options)) {
            $builder->setAttribute('disabled', $options['disabled']);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->groupClass,
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_group';
    }

}
