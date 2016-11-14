<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\Backoffice\Validator\Constraints\ContentTemplate;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ContentStrategy as BaseContentStrategy;
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
        return BaseContentStrategy::NAME === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('contentTemplateEnabled', 'checkbox', array(
            'label' => 'open_orchestra_backoffice.form.content_list.content_template_enabled.title',
            'attr' => array('help_text' => 'open_orchestra_backoffice.form.content_list.content_template_enabled.helper'),
            'required' => false,
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
        $builder->add('contentTemplate', 'oo_tinymce', array(
            'required' => false,
            'label' => 'open_orchestra_backoffice.form.content_list.content_template',
            'constraints' => new ContentTemplate(),
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
    }

    /**
     * @return array
     */
    public function getRequiredUriParameter()
    {
        return array('contentId');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content';
    }
}
