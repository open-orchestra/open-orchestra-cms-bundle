<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\Form\DataTransformer\ChoiceArrayToStringTransformer;
use OpenOrchestra\BackofficeBundle\Form\DataTransformer\ChoiceStringToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class OrchestraFieldChoice
 */
class OrchestraFieldChoice extends AbstractType
{
    protected $choiceArrayToStringTransformer;

    /**
     * @param ChoiceArrayToStringTransformer $choiceArrayToStringTransformer
     * @param ChoiceStringToArrayTransformer $choiceStringToArrayTransformer
     */
    public function __construct(
        ChoiceArrayToStringTransformer $choiceArrayToStringTransformer,
        ChoiceStringToArrayTransformer $choiceStringToArrayTransformer
    )
    {
        $this->choiceArrayToStringTransformer = $choiceArrayToStringTransformer;
        $this->choiceStringToArrayTransformer = $choiceStringToArrayTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['multiple'] === false) {
            $builder->addModelTransformer($this->choiceArrayToStringTransformer);
        } else {
            $builder->addModelTransformer($this->choiceStringToArrayTransformer);
        }
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return "orchestra_field_choice";
    }

}
