<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BlockChoiceType
 */
class BlockChoiceType extends AbstractType
{
    protected $choices;

    /**
     * @param array $choices
     */
    public function __construct(array $choices)
    {
        $this->choices = $choices;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'choices' => $this->getChoices(),
            )
        );
    }

    /**
     * @return array
     */
    protected function getChoices()
    {
        $choices = array();

        foreach ($this->choices as $key => $configuration) {
            $choices[$key] = $configuration['name'];
        }

        return $choices;
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oo_block_choice';
    }
}
