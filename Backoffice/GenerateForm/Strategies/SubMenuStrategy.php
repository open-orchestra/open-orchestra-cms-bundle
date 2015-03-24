<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\SubMenuStrategy as BaseSubMenuStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SubMenuStrategy
 */
class SubMenuStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return BaseSubMenuStrategy::SUBMENU === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nbLevel', 'text', array(
            'label' => 'open_orchestra_backoffice.form.sub_menu.level'
        ));
        $builder->add('nodeName', 'orchestra_node_choice', array(
            'label' => 'open_orchestra_backoffice.form.sub_menu.node',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sub_menu';
    }
}
