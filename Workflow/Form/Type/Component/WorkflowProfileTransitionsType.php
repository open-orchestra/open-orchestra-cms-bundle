<?php

namespace OpenOrchestra\Workflow\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class WorkflowProfileTransitionsType
 */
class WorkflowProfileTransitionsType extends AbstractType
{
    protected $dataClass;

    /**
     * @param string $dataClass
     */
    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
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
                'statuses' => $options['statuses']
         ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->dataClass,
            'statuses'   => array()
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
                $transitions[$statusFrom->getId() . '-' . $statusTo->getId()] = $statusFrom->getId() . '-' . $statusTo->getId();
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
