<?php

namespace OpenOrchestra\UserAdminBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType;
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

        if (array_key_exists('disabled', $options)) {
            $builder->setAttribute('disabled', $options['disabled']);
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oo_registration_user';
    }
}
