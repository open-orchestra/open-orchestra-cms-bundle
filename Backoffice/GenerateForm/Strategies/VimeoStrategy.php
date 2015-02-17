<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

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
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('videoId', 'orchestra_video', array(
            'label' => 'php_orchestra_backoffice.block.vimeo.video_id',
        ));
        $builder->add('width', 'text', array(
            'empty_data' => '480',
            'label' => 'php_orchestra_backoffice.block.vimeo.width',
            'required'  => false,
        ));
        $builder->add('height', 'text', array(
            'empty_data' => '269',
            'label' => 'php_orchestra_backoffice.block.vimeo.height',
            'required'  => false,
        ));
        $builder->add('color', 'orchestra_color_picker', array(
            'label' => 'php_orchestra_backoffice.block.vimeo.color',
            'required'  => false,
        ));
        $builder->add('autoplay', 'checkbox', array(
            'label' => 'php_orchestra_backoffice.block.vimeo.autoplay',
            'required'  => false,
        ));
        $builder->add('fullscreen', 'checkbox', array(
            'label' => 'php_orchestra_backoffice.block.vimeo.fullscreen',
            'required'  => false,
        ));
        $builder->add('title', 'checkbox', array(
            'label' => 'php_orchestra_backoffice.block.vimeo.title_video',
            'required'  => false,
        ));
        $builder->add('byline', 'checkbox', array(
            'label' => 'php_orchestra_backoffice.block.vimeo.byline',
            'required'  => false,
        ));
        $builder->add('portrait', 'checkbox', array(
            'label' => 'php_orchestra_backoffice.block.vimeo.portrait',
            'required'  => false,
        ));
        $builder->add('loop', 'checkbox', array(
            'label' => 'php_orchestra_backoffice.block.vimeo.loop',
            'required'  => false,
        ));
        $builder->add('badge', 'checkbox', array(
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
