<?php

namespace OpenOrchestra\UserAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserType
 */
class UserType extends AbstractType
{
    protected $class;
    protected $availableLanguages;

    /**
     * @param string $class
     * @param array  $availableLanguages
     */
    public function __construct($class, array $availableLanguages)
    {
        $this->class = $class;
        $this->availableLanguages = $availableLanguages;
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
        $builder->add('groups', 'oo_group_choice', array(
            'multiple' => true,
            'expanded' => true,
            'required' => false,
        ));
        $builder->add('language', 'choice', array(
            'choices' => $this->getLanguages(),
            'label' => 'open_orchestra_user.form.user.language'
        ));

        if (array_key_exists('disabled', $options)) {
            $builder->setAttribute('disabled', $options['disabled']);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
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
        return 'oo_user';
    }

    /**
     * @return array
     */
    protected function getLanguages()
    {
        $languages = array();

        foreach($this->availableLanguages as $language) {
            $languages[$language] = $language;
        }

        return $languages;
    }
}
