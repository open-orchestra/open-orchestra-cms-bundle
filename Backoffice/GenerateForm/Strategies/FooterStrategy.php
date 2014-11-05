<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use PHPOrchestra\ModelBundle\Repository\NodeRepository;
use Symfony\Component\Form\FormInterface;

/**
 * Class FooterStrategy
 */
class FooterStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::FOOTER === $block->getComponent();
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
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'footer';
    }

}
