<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\MenuStrategy as BaseMenuStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MenuStrategy
 */
class MenuStrategy  extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return BaseMenuStrategy::NAME === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'menu';
    }
}
