<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
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
        return DisplayBlockInterface::SEARCH === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value');
        $builder->add('nodeId', 'orchestra_node_choice');
        $builder->add('limit', 'integer');
    }

    /**
     * @return array
     */
    public function getDefaultConfiguration()
    {
        return array(
            'limit' => 7,
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'search';
    }

}
