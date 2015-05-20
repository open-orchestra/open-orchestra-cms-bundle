<?php

namespace OpenOrchestra\UserAdminBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use OpenOrchestra\UserAdminBundle\Form\DataTransformer\ChoicesOptionToArrayTransformer;

/**
 * Class UserType
 */
class UserType extends AbstractType
{
    protected $class;
    /**
     * @var ChoicesOptionToArrayTransformer
     */
    protected $choiceTransformer;

    /**
     * @param string              $class
     */
    public function __construct($class, ChoicesOptionToArrayTransformer $choiceTransformer)
    {
        $this->class = $class;
        $this->choiceTransformer = $choiceTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->choiceTransformer);
        $builder->add('firstName', 'text', array(
            'label' => 'open_orchestra_user.form.user.firstName'
        ));
        $builder->add('lastName', 'text', array(
            'label' => 'open_orchestra_user.form.user.lastName'
        ));
        $builder->add('email', 'email', array(
            'label' => 'open_orchestra_user.form.user.email'
        ));
        $builder->add('groups', 'orchestra_group', array(
            'multiple' => true,
            'expanded' => true,
            'required' => false,
        ));
        $builder->add('authorizations', 'collection', array(
            'type' => 'orchestra_workflow_function',
            'allow_add' => true,
            'label' => 'open_orchestra_user.form.user.authorizations',
            'attr' => array(
                'data-prototype-label-add' => 'Ajout',
                'data-prototype-label-new' => 'Nouveau',
                'data-prototype-label-remove' => 'Suppression',
            )
        ));
        $builder->add('language', 'orchestra_language', array(
            'label' => 'open_orchestra_user.form.user.language'
        ));

        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'user';
    }

}
