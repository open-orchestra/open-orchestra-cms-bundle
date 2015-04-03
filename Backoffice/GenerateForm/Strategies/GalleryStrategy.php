<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use OpenOrchestra\Media\Model\MediaInterface;
use OpenOrchestra\MediaBundle\DisplayBlock\Strategies\GalleryStrategy as BaseGalleryStrategy;

/**
 * Class GalleryStrategy
 */
class GalleryStrategy extends AbstractBlockStrategy
{
    protected $translator;
    protected $thumbnailConfig = array();

    /**
     * @param TranslatorInterface $translator
     * @param array               $thumbnailConfig
     */
    public function __construct(TranslatorInterface $translator, array $thumbnailConfig)
    {
        $this->translator = $translator;
        $this->thumbnailConfig = $thumbnailConfig;
    }

    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return BaseGalleryStrategy::GALLERY === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formats = $this->getFormats();
        $builder
            ->add('columnNumber', 'text', array(
                'empty_data' => 1,
                'label' => 'open_orchestra_backoffice.block.gallery.form.column_number',
            ))
            ->add('itemNumber', 'text', array(
                'empty_data' => 0,
                'label' => 'open_orchestra_backoffice.block.gallery.form.item_number.label',
                'attr' => array('help_text' => 'open_orchestra_backoffice.block.gallery.form.item_number.helper'),
            ))
            ->add('thumbnailFormat', 'choice', array(
                'choices' => $formats,
                'label' => 'open_orchestra_backoffice.block.gallery.form.thumbnail_format',
            ))
            ->add('imageFormat', 'choice', array(
                'choices' => $formats,
                'label' => 'open_orchestra_backoffice.block.gallery.form.image_format.label',
                'attr' => array('help_text' => 'open_orchestra_backoffice.block.gallery.form.image_format.helper'),
            ))
            ->add('pictures', 'collection', array(
                'type' => 'orchestra_media',
                'allow_add' => true,
                'attr' => array(
                    'data-prototype-label-add' => $this->translator->trans('open_orchestra_backoffice.block.gallery.form.media.add'),
                    'data-prototype-label-new' => $this->translator->trans('open_orchestra_backoffice.block.gallery.form.media.new'),
                    'data-prototype-label-remove' => $this->translator->trans('open_orchestra_backoffice.block.gallery.form.media.delete'),
                ),
                'label' => 'open_orchestra_backoffice.block.gallery.form.pictures',
            ))
            ;
    }

    /**
     * @return array
     */
    protected function getFormats()
    {
        $formats = array();
        $formats[MediaInterface::MEDIA_ORIGINAL] = $this->translator->trans('open_orchestra_backoffice.form.media.original_image');
        foreach ($this->thumbnailConfig as $key => $thumbnail) {
            $formats[$key] = $this->translator->trans('open_orchestra_backoffice.form.media.' . $key);
        }

        return $formats;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'gallery';
    }

}
