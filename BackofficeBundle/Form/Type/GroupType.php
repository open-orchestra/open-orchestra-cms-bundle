<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
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
            ->add('labels', 'translated_value_collection', array(
                'label' => 'open_orchestra_backoffice.form.group.label'
            ))
            ->add('roles', 'orchestra_role_choice', array(
                'label' => 'open_orchestra_backoffice.form.group.roles',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ))
            ->add('site', 'orchestra_site', array(
                'label' => 'open_orchestra_backoffice.form.group.site',
                'required' => false,
            ));

        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
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
        return 'group';
    }

}
