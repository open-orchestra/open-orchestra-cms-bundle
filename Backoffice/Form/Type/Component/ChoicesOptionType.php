<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use OpenOrchestra\Backoffice\Form\DataTransformer\ChoicesOptionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ChoicesOptionType
 */
class ChoicesOptionType extends AbstractType
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
        return "oo_choices_option";
    }

}
