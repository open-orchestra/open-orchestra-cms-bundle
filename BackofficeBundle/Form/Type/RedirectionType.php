<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\RedirectionTypeSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RedirectionType
 */
class RedirectionType extends AbstractType
{
    protected $redirectionClass;

    /**
     * @param string $redirectionClass
     */
    public function __construct($redirectionClass)
    {
        $this->redirectionClass = $redirectionClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('siteId', 'orchestra_site_choice', array(
            'label' => 'open_orchestra_backoffice.form.redirection.site_name',
        ));
        $builder->add('locale', 'orchestra_language',array(
            'label' => 'open_orchestra_backoffice.form.redirection.locale',
        ));
        $builder->add('routePattern', 'text',array(
            'label' => 'open_orchestra_backoffice.form.redirection.route_pattern',
        ));
        $builder->add('nodeId', 'orchestra_node_choice', array(
            'label' => 'open_orchestra_backoffice.form.redirection.node_id',
            'required' => false,
        ));
        $builder->add('url', 'text', array(
            'label' => 'open_orchestra_backoffice.form.redirection.url',
            'required' => false,
        ));
        $builder->add('permanent', 'checkbox', array(
            'label' => 'open_orchestra_backoffice.form.redirection.permanent',
            'required' => false,
        ));

        $builder->addEventSubscriber(new RedirectionTypeSubscriber());
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
            'data_class' => $this->redirectionClass,
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'redirection';
    }

}
