<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class TinymcewysiwygStrategy
 */
class TinyMCEWysiwygStrategy extends AbstractBlockStrategy
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
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('htmlContent', 'textarea', array(
            'attr' => array('class' => 'tinymce'),
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
