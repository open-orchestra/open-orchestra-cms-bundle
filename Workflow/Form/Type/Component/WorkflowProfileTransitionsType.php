<?php

namespace OpenOrchestra\Workflow\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OpenOrchestra\Workflow\Form\DataTransformer\ProfileTransitionsTransformer;

/**
 * Class WorkflowProfileTransitionsType
 */
class WorkflowProfileTransitionsType extends AbstractType
{
    protected $dataClass;
    protected $transitionTransformer;

    /**
     * @param string                        $dataClass
     * @apram ProfileTransitionsTransformer $transitionTransformer
     */
    public function __construct($dataClass, ProfileTransitionsTransformer $transitionTransformer)
    {
        $this->dataClass = $dataClass;
        $this->transitionTransformer = $transitionTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder
            ->add('transitions', 'oo_workflow_transitions_collection', array(
                'required' => false,
                'choices'  => $this->getChoices($options['statuses']),
                'statuses' => $options['statuses'],
                'locale'   => $options['locale']
         ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->dataClass,
            'statuses'   => array(),
            'locale'     => 'en'
        ));
    }

    /**
     * Generate all possible transitions between statuses
     *
     * @aprameter array $statuses
     *
     * @return array
     */
    protected function getChoices(array $statuses)
    {
        $transitions = array();

        foreach ($statuses as $statusFrom) {
            foreach ($statuses as $statusTo) {
                $transitionName = $this->transitionTransformer->generateTransitionName($statusFrom, $statusTo);
                $transitions[$transitionName] = $transitionName;
            }
        }

        return $transitions;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_workflow_profile_transitions';
    }
}
