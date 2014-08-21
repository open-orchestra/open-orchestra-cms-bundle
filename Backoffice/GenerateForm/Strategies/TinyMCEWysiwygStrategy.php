<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\Backoffice\GenerateForm\GenerateFormInterface;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class TinymcewysiwygStrategy
 */
class TinyMCEWysiwygStrategy implements GenerateFormInterface
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::TINYMCEWYSIWYG === $block->getComponent();
    }

    /**
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function buildForm(FormInterface $form, BlockInterface $block)
    {
        $attributes = $block->getAttributes();

        $form->add('htmlContent', 'textarea', array(
            'mapped' => false,
            'data' => array_key_exists('htmlContent', $attributes)? $attributes['htmlContent']: '',
            'attr' => array(
                'class' => 'tinymce'
            )
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tinymcewysiwyg';
    }

}
