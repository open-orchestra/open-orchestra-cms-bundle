<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use OpenOrchestra\Backoffice\EventSubscriber\RedirectionTypeSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use OpenOrchestra\Backoffice\Validator\Constraints\UniqueRedirection;

/**
 * Class RedirectionType
 */
class RedirectionType extends AbstractType
{
    const TYPE_INTERNAL = 'internal';
    const TYPE_EXTERNAL = 'external';

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
        $builder->add('siteId', 'oo_site_choice', array(
            'label'        => 'open_orchestra_backoffice.form.redirection.site_name',
            'group_id'     => 'redirection',
            'sub_group_id' => 'properties',
        ));
        $builder->add('locale', 'orchestra_language', array(
            'label'        => 'open_orchestra_backoffice.form.redirection.locale',
            'group_id'     => 'redirection',
            'sub_group_id' => 'properties',
        ));
        $builder->add('routePattern', 'text', array(
            'label'        => 'open_orchestra_backoffice.form.redirection.route_pattern',
            'group_id'     => 'redirection',
            'sub_group_id' => 'redirection',
        ));
        $builder->add('permanent', 'checkbox', array(
            'label'        => 'open_orchestra_backoffice.form.redirection.permanent',
            'required'     => false,
            'group_id'     => 'redirection',
            'sub_group_id' => 'redirection',
        ));
        $builder->add('tmp', 'oo_internal_external_link', array(
            'label'        => false,
            'required'     => false,
            'group_id'     => 'redirection',
            'sub_group_id' => 'redirection',
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
            'new_button' => false,
            'constraints'  => array(new UniqueRedirection()),
            'group_enabled' => true,
            'group_render' => array(
                'redirection' => array(
                    'rank'  => 0,
                    'label' => 'open_orchestra_backoffice.form.redirection.edit.title',
                ),
            ),
            'sub_group_render' => array(
                'properties' => array(
                    'rank'  => 0,
                    'label' => 'open_orchestra_backoffice.form.redirection.group.properties',
                ),
                'redirection' => array(
                    'rank'  => 10,
                    'label' => 'open_orchestra_backoffice.form.redirection.group.redirection',
                ),
            ),
        ));
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['new_button'] = $options['new_button'];
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_redirection';
    }
}
