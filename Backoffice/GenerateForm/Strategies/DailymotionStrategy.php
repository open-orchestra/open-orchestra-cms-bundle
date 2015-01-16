<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class DailymotionStrategy
 */
class DailymotionStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::DAILYMOTION === $block->getComponent();
    }

    /**
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function buildForm(FormInterface $form, BlockInterface $block)
    {
        $attributes = $block->getAttributes();

        $empty = array(
            'videoId' => '',
            'autoplay' => false,
            'info' => false,
            'background' => '',
            'foreground' => '',
            'highlight' => '',
            'width' => '25',
            'height' => '14'
        );

        $attributes = array_merge($empty, $attributes);

        $form->add('videoId', 'orchestra_video', array(
            'mapped' => false,
            'data' => $attributes['videoId'],
            'label' => 'php_orchestra_backoffice.block.dailymotion.video_id',
        ));
        $form->add('autoplay', 'orchestra_block_checkbox', array(
            'mapped' => false,
            'data' => $attributes['autoplay'],
            'label' => 'php_orchestra_backoffice.block.dailymotion.autoplay',
            'required'  => false,
        ));
        $form->add('info', 'orchestra_block_checkbox', array(
            'mapped' => false,
            'data' => $attributes['info'],
            'label' => 'php_orchestra_backoffice.block.dailymotion.info',
            'required'  => false,
        ));
        $form->add('background', 'text', array(
            'mapped' => false,
            'data' => $attributes['background'],
            'label' => 'php_orchestra_backoffice.block.dailymotion.background',
            'required'  => false,
        ));
        $form->add('foreground', 'text', array(
            'mapped' => false,
            'data' => $attributes['foreground'],
            'label' => 'php_orchestra_backoffice.block.dailymotion.foreground',
            'required'  => false,
        ));
        $form->add('highlight', 'text', array(
            'mapped' => false,
            'data' => $attributes['highlight'],
            'label' => 'php_orchestra_backoffice.block.dailymotion.highlight',
            'required'  => false,
        ));
        $form->add('width', 'text', array(
            'mapped' => false,
            'data' => $attributes['width'],
            'label' => 'php_orchestra_backoffice.block.dailymotion.width',
            'required'  => false,
        ));
        $form->add('height', 'text', array(
            'mapped' => false,
            'data' => $attributes['height'],
            'label' => 'php_orchestra_backoffice.block.dailymotion.height',
            'required'  => false,
        ));

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dailymotion';
    }
}
