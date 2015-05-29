<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\Form\DataTransformer\ChoicesOptionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class OrchestraChoicesOption
 */
class OrchestraChoicesOption extends AbstractType
{
    protected $choiceTransformer;

    /**
     * @param ChoicesOptionToArrayTransformer $choiceTransformer
     */
    public function __construct(ChoicesOptionToArrayTransformer $choiceTransformer)
    {
        $this->choiceTransformer = $choiceTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->choiceTransformer);
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return "orchestra_choices_option";
    }

}
