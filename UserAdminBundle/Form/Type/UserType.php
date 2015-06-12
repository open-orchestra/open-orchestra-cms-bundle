<?php

namespace OpenOrchestra\UserAdminBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Twig_Environment;

/**
 * Class UserType
 */
class UserType extends AbstractType
{
    protected $class;
    protected $languages;

    /**
     * @param string $class
     */
    public function __construct($class, Twig_Environment $twig)
    {
        $global = $twig->getGlobals();
        $this->class = $class;
        $this->languages = array();
        if (array_key_exists('available_languages', $global) && is_array($global['available_languages'])) {
            foreach($global['available_languages'] as $language) {
                $this->languages[$language] = $language;
            }
        }
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
        $builder->add('language', 'choice', array(
            'choices' => $this->languages,
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
