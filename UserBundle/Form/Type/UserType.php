<?php

namespace PHPOrchestra\UserBundle\Form\Type;

use PHPOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class UserType
 */
class UserType extends RegistrationUserType
{
    protected $translator;

    /**
     * @param string              $class
     * @param TranslatorInterface $translator
     */
    public function __construct($class, TranslatorInterface $translator)
    {
        parent::__construct($class, $translator);
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->remove('firstName')
            ->remove('lastName')
            ->remove('username');
        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
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
