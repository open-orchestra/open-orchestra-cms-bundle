<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
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

        $form->add('class', 'textarea', array(
            'mapped' => false,
            'data' => array_key_exists('class', $attributes)? json_encode($attributes['class']):'',
            'required' => false,
        ));
        $form->add('id', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('id', $attributes)? $attributes['id']:'',
            'required' => false,
        ));
        $form->add('nbLevel', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('nbLevel', $attributes)? $attributes['nbLevel']:2,
            'label' => 'php_orchestra_backoffice.form.sub_menu.level'
        ));
        $form->add('nodeName', 'orchestra_node_choice', array(
            'mapped' => false,
            'data' => array_key_exists('node', $attributes)? $attributes['node']:'',
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
