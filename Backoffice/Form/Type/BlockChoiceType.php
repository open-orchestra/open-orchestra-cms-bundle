<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BlockChoiceType
 */
class BlockChoiceType extends AbstractType
{
    /**
     * @param array $choices
     */
    public function __construct(array $choices)
    {
        foreach ($choices as $choice) {
            $this->choices[$choice] = 'open_orchestra_backoffice.block.' . $choice . '.title';
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'choices' => $this->choices,
            )
        );
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
