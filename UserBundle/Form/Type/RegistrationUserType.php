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
            ->add('firstName', 'text')
            ->add('lastName', 'text');

        parent::buildForm($builder, $options);

        $builder->add('roles', 'collection', array(
            'allow_add' => true,
            'attr' => array(
                'data-prototype-label-add' => $this->translator->trans('php_orchestra_backoffice.form.field_option.add'),
                'data-prototype-label-remove' => $this->translator->trans('php_orchestra_backoffice.form.field_option.delete'),
            )
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
