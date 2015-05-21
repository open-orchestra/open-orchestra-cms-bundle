<?php

namespace OpenOrchestra\MediaAdminBundle\GenerateForm\Strategies;

use OpenOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use OpenOrchestra\Media\Model\MediaInterface;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use OpenOrchestra\MediaBundle\DisplayBlock\Strategies\DisplayMediaStrategy as BaseMediaStrategy;

/**
 * Class DisplayMediaStrategy
 */
class DisplayMediaStrategy extends AbstractBlockStrategy
{
    protected $translator;
    protected $thumbnail = array();

    public function __construct(TranslatorInterface $translatorInterface, array $thumbnailConfig)
    {
        $this->translator = $translatorInterface;
        $this->thumbnail = $thumbnailConfig;
    }

    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return BaseMediaStrategy::DISPLAY_MEDIA == $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFormat', 'choice', array(
                'choices' => $this->getFormats(),
                'label' => 'open_orchestra_media_admin.block.gallery.form.image_format.label',
                'attr' => array('help_text' => 'open_orchestra_media_admin.block.gallery.form.image_format.helper'),
            ))
            ->add('picture', 'orchestra_media', array(
                'label' => 'open_orchestra_media_admin.block.gallery.form.pictures',
            ))
            ->add('nodeToLink', 'orchestra_node_choice', array(
                'label' => 'open_orchestra_media_admin.block.display_media.form.node_link',
                'required' => false
            ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'display_media';
    }

    /**
     * @return array
     */
    protected function getFormats()
    {
        $formats = array();
        $formats[MediaInterface::MEDIA_ORIGINAL] = $this->translator->trans('open_orchestra_media_admin.form.media.original_image');
        foreach ($this->thumbnail as $key => $thumbnail) {
            $formats[$key] = $this->translator->trans('open_orchestra_media_admin.form.media.' . $key);
        }

        return $formats;
    }
}
