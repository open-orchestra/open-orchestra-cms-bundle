<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use PHPOrchestra\Media\Model\MediaInterface;

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

        $this->formats[MediaInterface::MEDIA_ORIGINAL] = $this->translator->trans('php_orchestra_backoffice.form.media.original_image');
        foreach ($thumbnailConfig as $key => $thumbnail) {
            $this->formats[$key] = $this->translator->trans('php_orchestra_backoffice.form.media.' . $key);
        }
    }

    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::GALLERY === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nb_columns', 'text', array(
                'empty_data' => 1,
                'label' => $this->translator->trans('php_orchestra_backoffice.block.gallery.form.nb_columns')
            ))
            ->add('nb_items', 'text', array(
                'empty_data' => 0,
                'label' => $this->translator->trans('php_orchestra_backoffice.block.gallery.form.nb_items')
            ))
            ->add('thumbnail_format', 'choice', array(
                'choices' => $this->formats,
                'label' => $this->translator->trans('php_orchestra_backoffice.block.gallery.form.thumbnail_format')
            ))
            ->add('image_format', 'choice', array(
                'choices' => $this->formats,
                'label' => $this->translator->trans('php_orchestra_backoffice.block.gallery.form.image_format')
            ))
            ->add('pictures', 'collection', array(
                'type' => 'orchestra_media',
                'allow_add' => true,
                'attr' => array(
                    'data-prototype-label-add' => $this->translator->trans('php_orchestra_backoffice.block.gallery.form.media.add'),
                    'data-prototype-label-new' => $this->translator->trans('php_orchestra_backoffice.block.gallery.form.media.new'),
                    'data-prototype-label-remove' => $this->translator->trans('php_orchestra_backoffice.block.gallery.form.media.delete'),
                ),
                'label' => $this->translator->trans('php_orchestra_backoffice.block.gallery.form.pictures')
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
