<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class GroupType
 */
class GroupType extends AbstractType
{
    protected $groupClass;

    /**
     * @param string $groupClass
     */
    public function __construct($groupClass)
    {
        $this->groupClass = $groupClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, array(
            'label' => 'open_orchestra_backoffice.form.group.name',
        ));
        $builder->add('roles', 'orchestra_role_choice', array(
            'label' => 'open_orchestra_backoffice.form.group.roles',
            'multiple' => true,
            'expanded' => true,
            'required' => false,
        ));
        $builder->add('site', 'orchestra_site', array(
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
