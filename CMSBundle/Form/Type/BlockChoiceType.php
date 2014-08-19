<?php

namespace PHPOrchestra\CMSBundle\Form\Type;

/**
 * Class BlockChoiceType
 */
class BlockChoiceType extends OrchestraChoiceType
{
    /**
     * @param array $choices
     */
    public function __construct(array $choices)
    {
        foreach ($choices as $choice) {
            $this->choices[$choice] = $choice;
        }

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orchestra_block';
    }
}
