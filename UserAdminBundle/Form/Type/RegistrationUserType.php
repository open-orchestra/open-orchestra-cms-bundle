<?php

namespace OpenOrchestra\UserAdminBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType;
use OpenOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class UserType
 */
class RegistrationUserType extends RegistrationFormType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array(
                'label' => 'open_orchestra_user.form.registration_user.first_name'
            ))
            ->add('lastName', 'text', array(
                'label' => 'open_orchestra_user.form.registration_user.last_name'
            ));

        parent::buildForm($builder, $options);

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
