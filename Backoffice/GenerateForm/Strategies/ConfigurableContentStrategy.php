<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\Backoffice\GenerateForm\GenerateFormInterface;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class ConfigurableContentStrategy
 */
class ConfigurableContentStrategy implements GenerateFormInterface
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::CONFIGURABLE_CONTENT === $block->getComponent();
    }

    /**
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function buildForm(FormInterface $form, BlockInterface $block)
    {
        $attributes = $block->getAttributes();
        
        $form->template = 'PHPOrchestraBackofficeBundle:Editorial:ConfigurableContentForm.html.twig';
        
        $form
            ->add('contentTypeId', 'content_type_choice', array(
                'mapped' => false,
     //           'data' => array_key_exists('contentTypeId', $attributes)? $attributes['contentTypeId']: '',
                'label' => 'Content Type'
            ))
            ->add('contentId', 'choice', array(
                'mapped' => false,
     //           'data' => array_key_exists('contentId', $attributes)? $attributes['contentId']: '',
                'label' => 'Content'
            ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ConfigurableContent';
    }

}
