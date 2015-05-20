<?php

namespace OpenOrchestra\UserAdminBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use OpenOrchestra\UserAdminBundle\Form\DataTransformer\ContentTypeToAuthorizationTransformer;

/**
 * Class UserType
 */
class UserType extends AbstractType
{
    protected $class;
    protected $contentTypeToAuthorizationTransformer;

    /**
     * @param string                                $class
     * @param ContentTypeToAuthorizationTransformer $contentTypeToAuthorizationTransformer
     */
    public function __construct($class, ContentTypeToAuthorizationTransformer $contentTypeToAuthorizationTransformer)
    {
        $this->class = $class;
        $this->contentTypeToAuthorizationTransformer = $contentTypeToAuthorizationTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->contentTypeToAuthorizationTransformer);
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
            'type' => 'authorization',
            'label' => false,
            'required' => false,
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
