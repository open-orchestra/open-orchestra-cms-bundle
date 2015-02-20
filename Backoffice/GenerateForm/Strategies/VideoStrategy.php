<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class VideoStrategy
 */
class VideoStrategy extends AbstractBlockStrategy
{
    const TEMPLATE = 'PHPOrchestraBackofficeBundle:Block/Video:form.html.twig';

    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::VIDEO === $block->getComponent();
    }

    /**
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('videoType', 'choice', array(
            'label' => 'php_orchestra_backoffice.block.video.type',
            'choices' => array(
                'youtube' => 'youtube',
                'dailymotion' => 'dailymotion',
                'vimeo' => 'vimeo'
            )
        ));
        $this->addYoutubeForm($builder, $options);
        $this->addDailyMotionForm($builder, $options);
        $this->addVimeoForm($builder, $options);
    }

    /**
     * Add to the video form the youtube part
     * 
     * @param FormBuilderInterface $builder
     * 
     * @param array $options
     */
    protected function addYoutubeForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('youtubeVideoId', 'orchestra_video', array(
                'label' => 'php_orchestra_backoffice.block.video.youtube.video_id',
            ))
            ->add('youtubeWidth', 'text', array(
                'empty_data' => '480',
                'label' => 'php_orchestra_backoffice.block.video.youtube.width',
                'required'  => false,
            ))
            ->add('youtubeHeight', 'text', array(
                'empty_data' => '269',
                'label' => 'php_orchestra_backoffice.block.video.youtube.height',
                'required'  => false,
            ))
            ->add('youtubeAutoplay', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.block.video.youtube.autoplay',
                'required'  => false,
            ))
            ->add('youtubeFs', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.block.video.youtube.fs',
                'required'  => false,
            ))
            ->add('youtubeHl', 'orchestra_language', array(
                'label' => 'php_orchestra_backoffice.block.video.youtube.hl',
                'required'  => false,
            ))
            ->add('youtubeShowinfo', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.block.video.youtube.showinfo',
                'required'  => false,
            ))
            ->add('youtubeRel', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.block.video.youtube.rel',
                'required'  => false,
            ))
            ->add('youtubeDisablekb', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.block.video.youtube.disablekb',
                'required'  => false,
            ))
            ->add('youtubeLoop', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.block.video.youtube.loop',
                'required'  => false,
            ))
            ->add('youtubeControls', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.block.video.youtube.controls',
                'required'  => false,
            ))
            ->add('youtubeTheme', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.block.video.youtube.theme',
                'required'  => false,
            ))
            ->add('youtubeColor', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.block.video.youtube.color',
                'required'  => false,
            ))
        ;
    }

    /**
     * Add to the video form the dailymotion part
     * 
     * @param FormBuilderInterface $builder
     * 
     * @param array $options
     */
    protected function addDailyMotionForm($builder, $options)
    {
        $builder
            ->add('dailymotionVideoId', 'orchestra_video', array(
                'label' => 'php_orchestra_backoffice.block.video.dailymotion.video_id',
            ))
            ->add('dailymotionWidth', 'text', array(
                'empty_data' => '480',
                'label' => 'php_orchestra_backoffice.block.video.dailymotion.width',
                'required'  => false,
            ))
            ->add('dailymotionHeight', 'text', array(
                'empty_data' => '269',
                'label' => 'php_orchestra_backoffice.block.video.dailymotion.height',
                'required'  => false,
            ))
            ->add('dailymotionQuality', 'choice', array(
                'choices' => array('240' => '240', '380' => '380', '480' => '480', '720' => '720', '1080' => '1080'),
                'label' => 'php_orchestra_backoffice.block.video.dailymotion.quality',
                'required'  => false,
            ))
            ->add('dailymotionAutoplay', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.block.video.dailymotion.autoplay',
                'required'  => false,
            ))
            ->add('dailymotionInfo', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.block.video.dailymotion.info',
                'required'  => false,
            ))
            ->add('dailymotionRelated', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.block.video.dailymotion.related',
                'required'  => false,
            ))
            ->add('dailymotionChromeless', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.block.video.dailymotion.chromeless',
                'required'  => false,
            ))
            ->add('dailymotionLogo', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.block.video.dailymotion.logo',
                'required'  => false,
            ))
            ->add('dailymotionBackground', 'orchestra_color_picker', array(
                'label' => 'php_orchestra_backoffice.block.video.dailymotion.background',
                'required'  => false,
            ))
            ->add('dailymotionForeground', 'orchestra_color_picker', array(
                'label' => 'php_orchestra_backoffice.block.video.dailymotion.foreground',
                'required'  => false,
            ))
            ->add('dailymotionHighlight', 'orchestra_color_picker', array(
                'label' => 'php_orchestra_backoffice.block.video.dailymotion.highlight',
                'required'  => false,
            ))
        ;
    }

    /**
     * Add to the video form the vimeo part
     * 
     * @param FormBuilderInterface $builder
     * 
     * @param array $options
     */
    protected function addVimeoForm($builder, $options)
    {
        $builder
            ->add('vimeoVideoId', 'orchestra_video', array(
                'label' => 'php_orchestra_backoffice.block.video.vimeo.video_id',
            ))
            ->add('vimeoWidth', 'text', array(
                'empty_data' => '480',
                'label' => 'php_orchestra_backoffice.block.video.vimeo.width',
                'required'  => false,
            ))
            ->add('vimeoHeight', 'text', array(
                'empty_data' => '269',
                'label' => 'php_orchestra_backoffice.block.video.vimeo.height',
                'required'  => false,
            ))
            ->add('vimeoColor', 'orchestra_color_picker', array(
                'label' => 'php_orchestra_backoffice.block.video.vimeo.color',
                'required'  => false,
            ))
            ->add('vimeoAutoplay', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.block.video.vimeo.autoplay',
                'required'  => false,
            ))
            ->add('vimeoFullscreen', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.block.video.vimeo.fullscreen',
                'required'  => false,
            ))
            ->add('vimeoTitle', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.block.video.vimeo.title_video',
                'required'  => false,
            ))
            ->add('vimeoByline', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.block.video.vimeo.byline',
                'required'  => false,
            ))
            ->add('vimeoPortrait', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.block.video.vimeo.portrait',
                'required'  => false,
            ))
            ->add('vimeoLoop', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.block.video.vimeo.loop',
                'required'  => false,
            ))
            ->add('vimeoBadge', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.block.video.vimeo.badge',
                'required'  => false,
            ))
        ;
    }

    /**
     * Get block form template
     * 
     * @return string
     */
    public function getTemplate()
    {
        return self::TEMPLATE;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'video';
    }
}
