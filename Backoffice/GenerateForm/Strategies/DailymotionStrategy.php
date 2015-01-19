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
            'chromeless' => false,
            'autoplay' => false,
            'related' => false,
            'background' => '',
            'foreground' => '',
            'quality' => '',
            'highlight' => '',
            'height' => '269',
            'width' => '480',
            'info' => false,
            'logo' => false,
            'videoId' => '',
        );

        $attributes = array_merge($empty, $attributes);

        $form->add('videoId', 'orchestra_video', array(
            'mapped' => false,
            'data' => $attributes['videoId'],
            'label' => 'php_orchestra_backoffice.block.dailymotion.video_id',
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
        $form->add('quality', 'choice', array(
            'mapped' => false,
            'choices' => array('240' => '240', '380' => '380', '480' => '480', '720' => '720', '1080' => '1080'),
            'data' => $attributes['quality'],
            'label' => 'php_orchestra_backoffice.block.dailymotion.quality',
            'required'  => false,
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
        $form->add('related', 'orchestra_block_checkbox', array(
            'mapped' => false,
            'data' => $attributes['related'],
            'label' => 'php_orchestra_backoffice.block.dailymotion.related',
            'required'  => false,
        ));
        $form->add('chromeless', 'orchestra_block_checkbox', array(
            'mapped' => false,
            'data' => $attributes['chromeless'],
            'label' => 'php_orchestra_backoffice.block.dailymotion.chromeless',
            'required'  => false,
        ));
        $form->add('logo', 'orchestra_block_checkbox', array(
            'mapped' => false,
            'data' => $attributes['logo'],
            'label' => 'php_orchestra_backoffice.block.dailymotion.logo',
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
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dailymotion';
    }
}
