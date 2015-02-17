<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
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
        return DisplayBlockInterface::SUBMENU === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nbLevel', 'text', array(
            'label' => 'php_orchestra_backoffice.form.sub_menu.level'
        ));
        $builder->add('nodeName', 'orchestra_node_choice', array(
            'label' => 'php_orchestra_backoffice.form.sub_menu.node',
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
