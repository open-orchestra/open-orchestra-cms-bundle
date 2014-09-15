<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class CarrouselStrategy
 */
class CarrouselStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::CARROUSEL === $block->getComponent();
    }

    /**
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function buildForm(FormInterface $form, BlockInterface $block)
    {
        $attributes = $block->getAttributes();

        $form->add('pictures', 'textarea', array(
            'mapped' => false,
            'data' => array_key_exists('pictures', $attributes)? $attributes['pictures']:'',
        ));
        $form->add('width', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('width', $attributes)? $attributes['width']:'',
        ));
        $form->add('height', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('height', $attributes)? $attributes['height']:'',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'carrousel';
    }

}
