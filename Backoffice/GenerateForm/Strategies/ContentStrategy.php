<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ContentStrategy
 */
class ContentStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::CONTENT === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('contentTemplateEnabled', 'checkbox', array(
            'label' => 'open_orchestra_backoffice.form.content_list.content_template_enabled',
            'attr' => array('help_text' => 'open_orchestra_backoffice.form.content_list.content_template_enabled.helper'),
            'required' => false,
        ));
        $builder->add('contentTemplate', 'tinymce', array(
            'required' => false,
            'label' => 'open_orchestra_backoffice.form.content_list.content_template',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content';
    }
}
