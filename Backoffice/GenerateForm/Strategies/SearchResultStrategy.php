<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SearchResultStrategy
 */
class SearchResultStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return $this->getName() === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nodeId', 'text', array(
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
        $builder->add('nbdoc', 'integer', array(
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
        $builder->add('nbspellcheck', 'integer', array(
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
        $builder->add('limitField', 'integer', array(
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
        $builder->add('fielddisplayed', 'text', array(
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
        $builder->add('facets', 'text', array(
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
        $builder->add('filter', 'text', array(
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
        $builder->add('optionsearch', 'text', array(
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
        $builder->add('optionsdismax', 'text', array(
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Search_result';
    }

}
