<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class YoutubeStrategy
 */
class YoutubeStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::YOUTUBE === $block->getComponent();
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
            'fs' => false,
            'loop' => false,
            'showinfo' => false,
            'rel' => false,
            'disablekb' => false,
            'hl' => '',
//            'autohide' => '',
            'width' => '25',
            'height' => '14'
        );

        $attributes = array_merge($empty, $attributes);

        $form->add('videoId', 'orchestra_video', array(
            'mapped' => false,
            'data' => $attributes['videoId'],
            'label' => 'php_orchestra_backoffice.block.youtube.video_id',
        ));
        $form->add('autoplay', 'orchestra_block_checkbox', array(
            'mapped' => false,
            'data' => $attributes['autoplay'],
            'label' => 'php_orchestra_backoffice.block.youtube.autoplay',
            'required'  => false,
        ));
        $form->add('fs', 'orchestra_block_checkbox', array(
            'mapped' => false,
            'data' => $attributes['fs'],
            'label' => 'php_orchestra_backoffice.block.youtube.fs',
            'required'  => false,
        ));
        $form->add('hl', 'orchestra_language', array(
            'mapped' => false,
            'data' => $attributes['hl'],
            'label' => 'php_orchestra_backoffice.block.youtube.hl',
            'required'  => false,
        ));
        $form->add('showinfo', 'orchestra_block_checkbox', array(
            'mapped' => false,
            'data' => $attributes['showinfo'],
            'label' => 'php_orchestra_backoffice.block.youtube.showinfo',
            'required'  => false,
        ));
        $form->add('rel', 'orchestra_block_checkbox', array(
            'mapped' => false,
            'data' => $attributes['rel'],
            'label' => 'php_orchestra_backoffice.block.youtube.rel',
            'required'  => false,
        ));
        $form->add('disablekb', 'orchestra_block_checkbox', array(
            'mapped' => false,
            'data' => $attributes['disablekb'],
            'label' => 'php_orchestra_backoffice.block.youtube.disablekb',
            'required'  => false,
        ));
        $form->add('loop', 'orchestra_block_checkbox', array(
            'mapped' => false,
            'data' => $attributes['loop'],
            'label' => 'php_orchestra_backoffice.block.youtube.loop',
            'required'  => false,
        ));
        $form->add('width', 'text', array(
            'mapped' => false,
            'data' => $attributes['width'],
            'label' => 'php_orchestra_backoffice.block.youtube.width',
            'required'  => false,
        ));
        $form->add('height', 'text', array(
            'mapped' => false,
            'data' => $attributes['height'],
            'label' => 'php_orchestra_backoffice.block.youtube.height',
            'required'  => false,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'youtube';
    }
}
