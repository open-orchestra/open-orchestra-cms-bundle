<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use PHPOrchestra\ModelBundle\Repository\NodeRepository;
use Symfony\Component\Form\FormInterface;

/**
 * Class MenuStrategy
 */
class MenuStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::MENU === $block->getComponent();
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
            'data' => array_key_exists('class', $attributes)? json_encode($attributes['class']):json_encode(
                array(
                    'div' => 'divclass',
                    'ul' => 'ulclass',
                    'link' => 'linkclass'
                )
            ),
        ));
        $form->add('id', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('id', $attributes)? $attributes['id']:'',
        ));
        $form->add('nbLevel', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('nbLevel', $attributes)? $attributes['nbLevel']:4,
            'label' => 'php_orchestra_backoffice.form.menu.level'
        ));
        $form->add('nodeName', 'orchestra_node_choice', array(
            'mapped' => false,
            'data' => array_key_exists('node', $attributes)? $attributes['node']:'',
            'label' => 'php_orchestra_backoffice.form.menu.node',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'menu';
    }

}
