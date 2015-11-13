<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OpenOrchestra\BackofficeBundle\EventListener\TranslateValueInitializerListener;
use Symfony\Component\Form\FormEvents;

/**
 * Class GroupType
 */
class GroupType extends AbstractType
{
    protected $groupClass;
    protected $translateValueInitializer;

    /**
     * @param string                            $groupClass
     * @param TranslateValueInitializerListener $translateValueInitializer
     */
    public function __construct(
        $groupClass,
        TranslateValueInitializerListener $translateValueInitializer
    ) {
        $this->groupClass = $groupClass;
        $this->translateValueInitializer = $translateValueInitializer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this->translateValueInitializer, 'preSetData'));
        $builder
            ->add('name', null, array(
                'label' => 'open_orchestra_backoffice.form.group.name'
            ))
            ->add('labels', 'oo_translated_value_collection', array(
                'label' => 'open_orchestra_backoffice.form.group.label'
            ))
            ->add('site', 'oo_group_site_choice', array(
                'label' => 'open_orchestra_backoffice.form.group.site',
                'required' => false,
            ))
            ->add('roles', 'oo_role_choice', array(
                'label' => 'open_orchestra_backoffice.form.group.roles',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
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
