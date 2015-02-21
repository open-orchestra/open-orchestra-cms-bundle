<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class OrchestraChoiceType
 */
class OrchestraChoiceType extends AbstractType
{
    protected $choices;
    protected $name;

    /**
     * @param array  $choices
     * @param string $name
     */
    public function __construct(array $choices, $name)
    {
        $this->choices = $choices;
        $this->name = $name;
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
        return $this->name;
    }
}
