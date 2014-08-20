<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
            $this->choices[$choice] = $choice;
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
        return 'orchestra_block';
    }
}
