<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OpenOrchestra\Backoffice\Manager\TemplateManager;

/**
 * Class TemplateSetType
 */
class TemplateSetType extends AbstractType
{
    protected $choices;

    /**
     * @param TemplateManager $templateManager
     */
    public function __construct(TemplateManager $templateManager)
    {
        $this->choices = array();
        foreach ($this->templateManager->getTemplateSetParameters() as $key => $parameter) {
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
