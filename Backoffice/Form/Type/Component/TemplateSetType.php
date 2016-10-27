<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TemplateSetType
 */
class TemplateSetType extends AbstractType
{
    protected $choices;

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->choices = array();
        foreach ($parameters as $key => $parameter) {
            $this->choices[$key] = $parameter['label'];
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
        return 'oo_template_set';
    }
}
