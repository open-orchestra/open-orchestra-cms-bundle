<?php

namespace OpenOrchestra\Workflow\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use OpenOrchestra\Workflow\Form\DataTransformer\ProfileTransitionsTransformer;

/**
 * Class WorkflowTransitionsCollectionType
 */
class WorkflowTransitionsCollectionType extends AbstractType
{
    protected $transitionsTransformer;
    protected $defaultLocale;

    /**
     * @param ProfileTransitionsTransformer $transitionTransformer
     * @param string                        $defaultLocale
     */
    public function __construct(ProfileTransitionsTransformer $transitionTransformer, $defaultLocale)
    {
        $this->transitionsTransformer = $transitionTransformer;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->transitionsTransformer);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'expanded' => 'true',
                'multiple' => 'true',
                'required' => false,
                'statuses' => array(),
                'locale'   => $this->defaultLocale
            )
        );
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        foreach ($options['statuses'] as $status) {
            $view->vars['statuses'][$status->getId()] = $status->getLabel($options['locale']);
        }
    }

    /**
     * return string
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_workflow_transitions_collection';
    }
}
