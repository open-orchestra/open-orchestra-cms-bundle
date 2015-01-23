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
            'fullscreen' => false,
            'portrait' => false,
            'autoplay' => false,
            'byline' => false,
            'height' => '269',
            'title' => false,
            'width' => '480',
            'badge' => false,
            'videoId' => '',
            'loop' => false,
            'color' => '',
        );

        $attributes = array_merge($empty, $attributes);

        $form->add('videoId', 'orchestra_video', array(
            'mapped' => false,
            'data' => $attributes['videoId'],
            'label' => 'php_orchestra_backoffice.block.vimeo.video_id',
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
        $form->add('color', 'orchestra_color_picker', array(
            'mapped' => false,
            'data' => $attributes['color'],
            'label' => 'php_orchestra_backoffice.block.vimeo.color',
            'required'  => false,
        ));
        $form->add('autoplay', 'checkbox', array(
            'mapped' => false,
            'data' => $attributes['autoplay'],
            'label' => 'php_orchestra_backoffice.block.vimeo.autoplay',
            'required'  => false,
        ));
        $form->add('fullscreen', 'checkbox', array(
            'mapped' => false,
            'data' => $attributes['fullscreen'],
            'label' => 'php_orchestra_backoffice.block.vimeo.fullscreen',
            'required'  => false,
        ));
        $form->add('title', 'checkbox', array(
            'mapped' => false,
            'data' => $attributes['title'],
            'label' => 'php_orchestra_backoffice.block.vimeo.title_video',
            'required'  => false,
        ));
        $form->add('byline', 'checkbox', array(
            'mapped' => false,
            'data' => $attributes['byline'],
            'label' => 'php_orchestra_backoffice.block.vimeo.byline',
            'required'  => false,
        ));
        $form->add('portrait', 'checkbox', array(
            'mapped' => false,
            'data' => $attributes['portrait'],
            'label' => 'php_orchestra_backoffice.block.vimeo.portrait',
            'required'  => false,
        ));
        $form->add('loop', 'checkbox', array(
            'mapped' => false,
            'data' => $attributes['loop'],
            'label' => 'php_orchestra_backoffice.block.vimeo.loop',
            'required'  => false,
        ));
        $form->add('badge', 'checkbox', array(
            'mapped' => false,
            'data' => $attributes['badge'],
            'label' => 'php_orchestra_backoffice.block.vimeo.badge',
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
