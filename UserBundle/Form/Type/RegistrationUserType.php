<?php

namespace PHPOrchestra\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType;
use PHPOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class UserType
 */
class RegistrationUserType extends RegistrationFormType
{
    protected $translator;

    /**
     * @param string              $class
     * @param TranslatorInterface $translator
     */
    public function __construct($class, TranslatorInterface $translator)
    {
        parent::__construct($class);
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array(
                'label' => 'php_orchestra_user.form.registration_user.first_name'
            ))
            ->add('lastName', 'text', array(
                'label' => 'php_orchestra_user.form.registration_user.last_name'
            ));

        parent::buildForm($builder, $options);

        $builder->add('roles', 'collection', array(
            'type' => 'orchestra_role_choice',
            'allow_add' => true,
            'allow_delete' => true,
            'attr' => array(
                'data-prototype-label-add' => $this->translator->trans('php_orchestra_backoffice.form.field_option.add'),
                'data-prototype-label-remove' => $this->translator->trans('php_orchestra_backoffice.form.field_option.delete'),
            ),
            'label' => 'php_orchestra_user.form.registration_user.roles'
        ));

        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'registration_user';
    }
}
