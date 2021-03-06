<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ContentListStrategy as BaseContentListStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Class ContentListStrategy
 */
class ContentListStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return BaseContentListStrategy::NAME === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('contentNodeId', 'oo_node_choice', array(
            'label' => 'open_orchestra_backoffice.form.content_list.node',
            'constraints' => new NotBlank(),
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
        $builder->add('characterNumber', 'integer', array(
            'empty_data' => 50,
            'constraints' => new Type('integer'),
            'label' => 'open_orchestra_backoffice.form.content_list.nb_characters',
            'required' => false,
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
        $builder->add('contentSearch', 'oo_content_search', array(
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
    }

    /**
     * @return array
     */
    public function getDefaultConfiguration()
    {
        return array(
            'contentNodeId' => NodeInterface::ROOT_NODE_ID,
            'characterNumber' => 50
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content_list';
    }
}
