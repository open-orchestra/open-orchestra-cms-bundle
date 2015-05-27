<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
        $builder
            ->add('name', 'text', array('label' => 'open_orchestra_backoffice.form.api_client.name'))
            ->add('trusted', 'checkbox', array(
                    'label' => 'open_orchestra_backoffice.form.api_client.trusted',
                    'required' => false
            ))
            ->add('key', 'text', array('disabled' => true, 'label' => 'open_orchestra_backoffice.form.api_client.key'))
            ->add('secret', 'text', array('disabled' => true, 'label' => 'open_orchestra_backoffice.form.api_client.secret'))
        ;
        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => $this->class));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'api_client';
    }
}
