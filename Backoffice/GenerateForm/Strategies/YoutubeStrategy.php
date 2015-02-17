<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

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
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('videoId', 'orchestra_video', array(
            'label' => 'php_orchestra_backoffice.block.youtube.video_id',
        ));
        $builder->add('width', 'text', array(
            'empty_data' => '480',
            'label' => 'php_orchestra_backoffice.block.youtube.width',
            'required'  => false,
        ));
        $builder->add('height', 'text', array(
            'empty_data' => '269',
            'label' => 'php_orchestra_backoffice.block.youtube.height',
            'required'  => false,
        ));
        $builder->add('autoplay', 'checkbox', array(
            'label' => 'php_orchestra_backoffice.block.youtube.autoplay',
            'required'  => false,
        ));
        $builder->add('fs', 'checkbox', array(
            'label' => 'php_orchestra_backoffice.block.youtube.fs',
            'required'  => false,
        ));
        $builder->add('hl', 'orchestra_language', array(
            'label' => 'php_orchestra_backoffice.block.youtube.hl',
            'required'  => false,
        ));
        $builder->add('showinfo', 'checkbox', array(
            'label' => 'php_orchestra_backoffice.block.youtube.showinfo',
            'required'  => false,
        ));
        $builder->add('rel', 'checkbox', array(
            'label' => 'php_orchestra_backoffice.block.youtube.rel',
            'required'  => false,
        ));
        $builder->add('disablekb', 'checkbox', array(
            'label' => 'php_orchestra_backoffice.block.youtube.disablekb',
            'required'  => false,
        ));
        $builder->add('loop', 'checkbox', array(
            'label' => 'php_orchestra_backoffice.block.youtube.loop',
            'required'  => false,
        ));
        $builder->add('controls', 'checkbox', array(
            'label' => 'php_orchestra_backoffice.block.youtube.controls',
            'required'  => false,
        ));
        $builder->add('theme', 'checkbox', array(
            'label' => 'php_orchestra_backoffice.block.youtube.theme',
            'required'  => false,
        ));
        $builder->add('color', 'checkbox', array(
            'label' => 'php_orchestra_backoffice.block.youtube.color',
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
