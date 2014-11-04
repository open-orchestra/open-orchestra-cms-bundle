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
    protected $nodeRepository;

    /**
     * @param NodeRepository $nodeRepository
     */
    public function __construct(NodeRepository $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;
    }

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
        $nodes = $this->nodeRepository->findLastVersionBySiteId();
        $newNodes = array_map(function($element) {
            return $element->getName();
        }, $nodes);

        $attributes = $block->getAttributes();

        $form->add('class', 'textarea', array(
            'mapped' => false,
            'data' => array_key_exists('class', $attributes)? json_encode($attributes['class']):'',
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
        $form->add('nodeName', 'choice', array(
            'choices' => $newNodes,
            'mapped' => false,
            'data' => array_key_exists('node', $attributes)? $attributes['node']:'root',
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
