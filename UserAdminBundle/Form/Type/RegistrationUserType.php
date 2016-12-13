<?php

namespace OpenOrchestra\UserAdminBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class UserType
 */
class RegistrationUserType extends UserType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array(
                'label' => 'form.username',
                'translation_domain' => 'FOSUserBundle',
                'group_id' => 'information',
                'sub_group_id' => 'contact_information',
            ));

        parent::buildForm($builder, $options);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oo_registration_user';
    }
}
