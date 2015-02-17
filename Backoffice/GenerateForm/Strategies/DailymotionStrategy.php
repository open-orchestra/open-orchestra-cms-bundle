<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

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
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('videoId', 'orchestra_video', array(
            'label' => 'php_orchestra_backoffice.block.dailymotion.video_id',
        ));
        $builder->add('width', 'text', array(
            'empty_data' => '480',
            'label' => 'php_orchestra_backoffice.block.dailymotion.width',
            'required'  => false,
        ));
        $builder->add('height', 'text', array(
            'empty_data' => '269',
            'label' => 'php_orchestra_backoffice.block.dailymotion.height',
            'required'  => false,
        ));
        $builder->add('quality', 'choice', array(
            'choices' => array('240' => '240', '380' => '380', '480' => '480', '720' => '720', '1080' => '1080'),
            'label' => 'php_orchestra_backoffice.block.dailymotion.quality',
            'required'  => false,
        ));
        $builder->add('autoplay', 'checkbox', array(
            'label' => 'php_orchestra_backoffice.block.dailymotion.autoplay',
            'required'  => false,
        ));
        $builder->add('info', 'checkbox', array(
            'label' => 'php_orchestra_backoffice.block.dailymotion.info',
            'required'  => false,
        ));
        $builder->add('related', 'checkbox', array(
            'label' => 'php_orchestra_backoffice.block.dailymotion.related',
            'required'  => false,
        ));
        $builder->add('chromeless', 'checkbox', array(
            'label' => 'php_orchestra_backoffice.block.dailymotion.chromeless',
            'required'  => false,
        ));
        $builder->add('logo', 'checkbox', array(
            'label' => 'php_orchestra_backoffice.block.dailymotion.logo',
            'required'  => false,
        ));
        $builder->add('background', 'orchestra_color_picker', array(
            'label' => 'php_orchestra_backoffice.block.dailymotion.background',
            'required'  => false,
        ));
        $builder->add('foreground', 'orchestra_color_picker', array(
            'label' => 'php_orchestra_backoffice.block.dailymotion.foreground',
            'required'  => false,
        ));
        $builder->add('highlight', 'orchestra_color_picker', array(
            'label' => 'php_orchestra_backoffice.block.dailymotion.highlight',
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
