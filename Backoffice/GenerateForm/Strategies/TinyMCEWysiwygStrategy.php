<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\TinyMCEWysiwygStrategy as BaseTinyMCEWysiwygStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
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
        return BaseTinyMCEWysiwygStrategy::TINYMCEWYSIWYG === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('htmlContent', 'tinymce');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tinymcewysiwyg';
    }

}
