<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class AddThisStrategy
 */
class AddThisStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::ADDTHIS === $block->getComponent();
    }

    /**
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function buildForm(FormInterface $form, BlockInterface $block)
    {
        $attributes = $block->getAttributes();

        $empty = array(
            'pubid' => '',
            'addThisClass' => '',
        );

        $attributes = array_merge($empty, $attributes);

        $form->add('pubid', 'text', array(
            'mapped' => false,
            'data' => $attributes['pubid'],
        ));
        $form->add('addThisClass', 'textarea', array(
            'mapped' => false,
            'data' => $attributes['addThisClass'],
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'add_this';
    }
}
