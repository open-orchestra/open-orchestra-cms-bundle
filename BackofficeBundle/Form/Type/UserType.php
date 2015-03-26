<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\UserBundle\Form\Type\UserType as BaseUserType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class UserType
 */
class UserType extends BaseUserType
{
    /**
     * @param string              $class
     * @param TranslatorInterface $translator
     */
    public function __construct($class, TranslatorInterface $translator)
    {
        parent::__construct($class, $translator);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('groups', 'orchestra_group', array(
            'multiple' => true,
            'expanded' => true,
            'required' => false,
        ));
        $builder->add('language', 'orchestra_language', array(
            'label' => 'open_orchestra_user.form.user.language'
        ));
    }
}
