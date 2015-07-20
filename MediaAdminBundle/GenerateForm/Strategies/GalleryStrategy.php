<?php

namespace OpenOrchestra\MediaAdminBundle\GenerateForm\Strategies;

use OpenOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use OpenOrchestra\Media\Model\MediaInterface;
use OpenOrchestra\Media\DisplayBlock\Strategies\GalleryStrategy as BaseGalleryStrategy;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

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
            ->add('id', 'text', array(
                'label' => 'open_orchestra_backoffice.form.block.id',
                'constraints' => new NotBlank(),
            ))
            ->add('thumbnailFormat', 'choice', array(
                'choices' => $formats,
                'label' => 'open_orchestra_media_admin.block.gallery.form.thumbnail_format',
                'constraints' => new NotBlank(),
            ))
            ->add('imageFormat', 'choice', array(
                'choices' => $formats,
                'constraints' => new NotBlank(),
                'label' => 'open_orchestra_media_admin.block.gallery.form.image_format.label',
                'attr' => array('help_text' => 'open_orchestra_media_admin.block.gallery.form.image_format.helper'),
            ))
            ->add('pictures', 'collection', array(
                'type' => 'orchestra_media',
                'constraints' => new NotBlank(),
                'allow_add' => true,
                'attr' => array(
                    'data-prototype-label-add' => $this->translator->trans('open_orchestra_media_admin.block.gallery.form.media.add'),
                    'data-prototype-label-new' => $this->translator->trans('open_orchestra_media_admin.block.gallery.form.media.new'),
                    'data-prototype-label-remove' => $this->translator->trans('open_orchestra_media_admin.block.gallery.form.media.delete'),
                ),
                'label' => 'open_orchestra_media_admin.block.gallery.form.pictures',
            ))
            ;
    }

    /**
     * @return array
     */
    protected function getFormats()
    {
        $formats = array();
        $formats[MediaInterface::MEDIA_ORIGINAL] = $this->translator->trans('open_orchestra_media_admin.form.media.original_image');
        foreach ($this->thumbnailConfig as $key => $thumbnail) {
            $formats[$key] = $this->translator->trans('open_orchestra_media_admin.form.media.' . $key);
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
