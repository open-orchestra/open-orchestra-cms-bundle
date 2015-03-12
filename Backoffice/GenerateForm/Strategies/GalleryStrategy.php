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
    protected $formats = array();

    /**
     * @param TranslatorInterface $translator
     * @param array               $thumbnailConfig
     */
    public function __construct(TranslatorInterface $translator, array $thumbnailConfig)
    {
        $this->translator = $translator;

        $this->formats[MediaInterface::MEDIA_ORIGINAL] = $this->translator->trans('open_orchestra_backoffice.form.media.original_image');
        foreach ($thumbnailConfig as $key => $thumbnail) {
            $this->formats[$key] = $this->translator->trans('open_orchestra_backoffice.form.media.' . $key);
        }
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
        $builder
            ->add('columnNumber', 'text', array(
                'empty_data' => 1,
                'label' => 'open_orchestra_backoffice.block.gallery.form.column_number'
            ))
            ->add('itemNumber', 'text', array(
                'empty_data' => 0,
                'label' => 'open_orchestra_backoffice.block.gallery.form.item_number'
            ))
            ->add('thumbnailFormat', 'choice', array(
                'choices' => $this->formats,
                'label' => 'open_orchestra_backoffice.block.gallery.form.thumbnail_format'
            ))
            ->add('imageFormat', 'choice', array(
                'choices' => $this->formats,
                'label' => $this->translator->trans('open_orchestra_backoffice.block.gallery.form.image_format')
            ))
            ->add('pictures', 'collection', array(
                'type' => 'orchestra_media',
                'allow_add' => true,
                'attr' => array(
                    'data-prototype-label-add' => $this->translator->trans('open_orchestra_backoffice.block.gallery.form.media.add'),
                    'data-prototype-label-new' => $this->translator->trans('open_orchestra_backoffice.block.gallery.form.media.new'),
                    'data-prototype-label-remove' => $this->translator->trans('open_orchestra_backoffice.block.gallery.form.media.delete'),
                ),
                'label' => $this->translator->trans('open_orchestra_backoffice.block.gallery.form.pictures')
            ))
            ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gallery';
    }

}
