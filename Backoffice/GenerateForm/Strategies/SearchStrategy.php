<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SearchStrategy
 */
class SearchStrategy extends AbstractBlockStrategy
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
        $builder->add('value', 'text', array(
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
        $builder->add('nodeId', 'oo_node_choice', array(
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
        $builder->add('limit', 'integer', array(
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
    }

    /**
     * @return array
     */
    public function getDefaultConfiguration()
    {
        return array('limit' => 7);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'search';
    }

}
