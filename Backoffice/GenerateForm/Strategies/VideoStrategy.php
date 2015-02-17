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
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'video';
    }
}
