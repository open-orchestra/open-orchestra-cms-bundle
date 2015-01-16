<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class VimeoStrategy
 */
class VimeoStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::VIMEO === $block->getComponent();
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
            'fullscreen' => false,
            'color' => '',
            'title' => false,
            'byline' => false,
            'portrait' => false,
            'loop' => false,
            'width' => '25',
            'height' => '14'
        );

        $attributes = array_merge($empty, $attributes);

        $form->add('videoId', 'orchestra_video', array(
            'mapped' => false,
            'data' => $attributes['videoId'],
            'label' => 'php_orchestra_backoffice.block.vimeo.video_id',
        ));
        $form->add('autoplay', 'orchestra_block_checkbox', array(
            'mapped' => false,
            'data' => $attributes['autoplay'],
            'label' => 'php_orchestra_backoffice.block.vimeo.autoplay',
            'required'  => false,
        ));
        $form->add('fullscreen', 'orchestra_block_checkbox', array(
            'mapped' => false,
            'data' => $attributes['fullscreen'],
            'label' => 'php_orchestra_backoffice.block.vimeo.fullscreen',
            'required'  => false,
        ));
        $form->add('color', 'text', array(
            'mapped' => false,
            'data' => $attributes['color'],
            'label' => 'php_orchestra_backoffice.block.vimeo.color',
            'required'  => false,
        ));
        $form->add('title', 'orchestra_block_checkbox', array(
            'mapped' => false,
            'data' => $attributes['title'],
            'label' => 'php_orchestra_backoffice.block.vimeo.title',
            'required'  => false,
        ));
        $form->add('byline', 'orchestra_block_checkbox', array(
            'mapped' => false,
            'data' => $attributes['byline'],
            'label' => 'php_orchestra_backoffice.block.vimeo.byline',
            'required'  => false,
        ));
        $form->add('portrait', 'orchestra_block_checkbox', array(
            'mapped' => false,
            'data' => $attributes['portrait'],
            'label' => 'php_orchestra_backoffice.block.vimeo.portrait',
            'required'  => false,
        ));
        $form->add('loop', 'orchestra_block_checkbox', array(
            'mapped' => false,
            'data' => $attributes['loop'],
            'label' => 'php_orchestra_backoffice.block.vimeo.loop',
            'required'  => false,
        ));
        $form->add('width', 'text', array(
            'mapped' => false,
            'data' => $attributes['width'],
            'label' => 'php_orchestra_backoffice.block.vimeo.width',
            'required'  => false,
        ));
        $form->add('height', 'text', array(
            'mapped' => false,
            'data' => $attributes['height'],
            'label' => 'php_orchestra_backoffice.block.vimeo.height',
            'required'  => false,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'vimeo';
    }
}
