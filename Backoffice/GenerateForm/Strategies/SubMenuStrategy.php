<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

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
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function buildForm(FormInterface $form, BlockInterface $block)
    {
        $attributes = $block->getAttributes();

        $empty = array(
            'nbLevel' => 2,
            'nodeName' => ''
        );

        $attributes = array_merge($empty, $attributes);

        $form->add('nbLevel', 'text', array(
            'mapped' => false,
            'data' => $attributes['nbLevel'],
            'label' => 'php_orchestra_backoffice.form.sub_menu.level'
        ));
        $form->add('nodeName', 'orchestra_node_choice', array(
            'mapped' => false,
            'data' => $attributes['nodeName'],
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
