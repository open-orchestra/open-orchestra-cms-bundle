<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ApiClientType
 */
class ApiClientType extends AbstractType
{
    protected $class;

    /**
     * @param string $class
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('label' => 'open_orchestra_backoffice.form.api_client.name'));
        $builder->add('trusted', 'checkbox', array(
                    'label' => 'open_orchestra_backoffice.form.api_client.trusted',
                    'required' => false
            ));
        $builder->add('roles', 'orchestra_role_choice', array(
            'label' => 'open_orchestra_backoffice.form.group.roles',
            'multiple' => true,
            'expanded' => true,
            'required' => false,
        ));
        $builder->add('key', 'text', array('disabled' => true, 'label' => 'open_orchestra_backoffice.form.api_client.key'));
        $builder->add('secret', 'text', array('disabled' => true, 'label' => 'open_orchestra_backoffice.form.api_client.secret'));
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
            'data_class' => $this->class,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'api_client';
    }
}
