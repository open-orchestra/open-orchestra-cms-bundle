<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\VideoStrategy as BaseVideoStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class VideoStrategy
 */
class VideoStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return BaseVideoStrategy::NAME === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('videoType', 'choice', array(
            'label' => 'open_orchestra_backoffice.block.video.type',
            'choices' => array(
                'youtube' => 'open_orchestra_backoffice.block.video.youtube.name',
                'dailymotion' => 'open_orchestra_backoffice.block.video.dailymotion.name',
                'vimeo' => 'open_orchestra_backoffice.block.video.vimeo.name'
            ),
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
        $this->addYoutubeForm($builder);
        $this->addDailyMotionForm($builder);
        $this->addVimeoForm($builder);
    }

    /**
     * Add to the video form the youtube part
     *
     * @param FormBuilderInterface $builder
     */
    protected function addYoutubeForm(FormBuilderInterface $builder)
    {

        $builder
            ->add('youtubeVideoId', 'oo_video', array(
                'label' => 'open_orchestra_backoffice.block.video.youtube.video_id',
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'youtube'
                )
            ))
            ->add('youtubeWidth', 'text', array(
                'empty_data' => '480',
                'label' => 'open_orchestra_backoffice.block.video.youtube.width',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'youtube'
                )
            ))
            ->add('youtubeHeight', 'text', array(
                'empty_data' => '269',
                'label' => 'open_orchestra_backoffice.block.video.youtube.height',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'youtube'
                )
            ))
            ->add('youtubeAutoplay', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.block.video.youtube.autoplay',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'youtube'
                )
            ))
            ->add('youtubeFs', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.block.video.youtube.fs',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'youtube'
                )
            ))
            ->add('youtubeHl', 'orchestra_language', array(
                'label' => 'open_orchestra_backoffice.block.video.youtube.hl',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
            ))
            ->add('youtubeShowinfo', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.block.video.youtube.showinfo',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'youtube'
                )
            ))
            ->add('youtubeRel', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.block.video.youtube.rel.title',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array('data-video-type' => 'youtube', 'help_text' => 'open_orchestra_backoffice.block.video.youtube.rel.helper'),
            ))
            ->add('youtubeDisablekb', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.block.video.youtube.disablekb',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'youtube'
                )
            ))
            ->add('youtubeLoop', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.block.video.youtube.loop',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'youtube'
                )
            ))
            ->add('youtubeControls', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.block.video.youtube.controls',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'youtube'
                )
            ))
            ->add('youtubeTheme', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.block.video.youtube.theme',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'youtube'
                )
            ))
            ->add('youtubeColor', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.block.video.youtube.color',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'youtube'
                )
            ))
        ;
    }

    /**
     * Add to the video form the dailymotion part
     *
     * @param FormBuilderInterface $builder
     */
    protected function addDailyMotionForm($builder)
    {
        $builder
            ->add('dailymotionVideoId', 'oo_video', array(
                'label' => 'open_orchestra_backoffice.block.video.dailymotion.video_id',
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'dailymotion'
                )
            ))
            ->add('dailymotionWidth', 'text', array(
                'empty_data' => '480',
                'label' => 'open_orchestra_backoffice.block.video.dailymotion.width',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'dailymotion'
                )
            ))
            ->add('dailymotionHeight', 'text', array(
                'empty_data' => '269',
                'label' => 'open_orchestra_backoffice.block.video.dailymotion.height',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'dailymotion'
                )
            ))
            ->add('dailymotionQuality', 'choice', array(
                'choices' => array('240' => '240', '380' => '380', '480' => '480', '720' => '720', '1080' => '1080'),
                'label' => 'open_orchestra_backoffice.block.video.dailymotion.quality',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'dailymotion'
                )
            ))
            ->add('dailymotionAutoplay', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.block.video.dailymotion.autoplay',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'dailymotion'
                )
            ))
            ->add('dailymotionInfo', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.block.video.dailymotion.info',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'dailymotion'
                )
            ))
            ->add('dailymotionRelated', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.block.video.dailymotion.related',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'dailymotion'
                )
            ))
            ->add('dailymotionChromeless', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.block.video.dailymotion.chromeless',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'dailymotion'
                )
            ))
            ->add('dailymotionLogo', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.block.video.dailymotion.logo',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'dailymotion'
                )
            ))
            ->add('dailymotionBackground', 'oo_color_picker', array(
                'label' => 'open_orchestra_backoffice.block.video.dailymotion.background',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'dailymotion'
                )
            ))
            ->add('dailymotionForeground', 'oo_color_picker', array(
                'label' => 'open_orchestra_backoffice.block.video.dailymotion.foreground',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'dailymotion'
                )
            ))
            ->add('dailymotionHighlight', 'oo_color_picker', array(
                'label' => 'open_orchestra_backoffice.block.video.dailymotion.highlight',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'dailymotion'
                )
            ))
        ;
    }

    /**
     * Add to the video form the vimeo part
     *
     * @param FormBuilderInterface $builder
     */
    protected function addVimeoForm($builder)
    {
        $builder
            ->add('vimeoVideoId', 'oo_video', array(
                'label' => 'open_orchestra_backoffice.block.video.vimeo.video_id',
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'vimeo'
                )
            ))
            ->add('vimeoWidth', 'text', array(
                'empty_data' => '480',
                'label' => 'open_orchestra_backoffice.block.video.vimeo.width',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'vimeo'
                )
            ))
            ->add('vimeoHeight', 'text', array(
                'empty_data' => '269',
                'label' => 'open_orchestra_backoffice.block.video.vimeo.height',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'vimeo'
                )
            ))
            ->add('vimeoColor', 'oo_color_picker', array(
                'label' => 'open_orchestra_backoffice.block.video.vimeo.color',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'vimeo',
                    'class' => 'colorpicker'
                )
            ))
            ->add('vimeoAutoplay', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.block.video.vimeo.autoplay',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'vimeo'
                )
            ))
            ->add('vimeoFullscreen', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.block.video.vimeo.fullscreen',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'vimeo'
                )
            ))
            ->add('vimeoTitle', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.block.video.vimeo.title_video',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'vimeo'
                )
            ))
            ->add('vimeoByline', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.block.video.vimeo.byline',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'vimeo'
                )
            ))
            ->add('vimeoPortrait', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.block.video.vimeo.portrait',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'vimeo'
                )
            ))
            ->add('vimeoLoop', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.block.video.vimeo.loop',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'vimeo'
                )
            ))
            ->add('vimeoBadge', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.block.video.vimeo.badge',
                'required'  => false,
                'group_id' => 'data',
                'sub_group_id' => 'content',
                'attr' => array(
                    'data-video-type' => 'vimeo'
                )
            ))
        ;
    }

    /**
     * @return array
     */
    public function getDefaultConfiguration()
    {
        return array(
            'youtubeAutoplay' => false,
            'youtubeShowinfo' => false,
            'youtubeFs' => false,
            'youtubeRel' => false,
            'youtubeDisablekb' => false,
            'youtubeLoop' => false,
            'youtubeControls' => false,
            'youtubeTheme' => false,
            'youtubeColor' => false,
            'dailymotionAutoplay' => false,
            'dailymotionInfo' => false,
            'dailymotionLogo' => false,
            'dailymotionRelated' => false,
            'dailymotionChromeless' => false,
            'vimeoAutoplay' => false,
            'vimeoTitle' => false,
            'vimeoFullscreen' => false,
            'vimeoByline' => false,
            'vimeoPortrait' => false,
            'vimeoLoop' => false,
            'vimeoBadge' => false,
            'vimeoColor' => false,
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'video';
    }
}
