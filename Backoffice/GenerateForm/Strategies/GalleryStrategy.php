<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class GalleryStrategy
 */
class GalleryStrategy extends AbstractBlockStrategy
{
    protected $translator;
    protected $thumbnailConfig;

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
        return DisplayBlockInterface::GALLERY === $block->getComponent();
    }

    /**
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function buildForm(FormInterface $form, BlockInterface $block)
    {
        $attributes = $block->getAttributes();

        $form
            ->add('nb_columns', 'text', array(
                'mapped' => false,
                'data' => array_key_exists('nb_columns', $attributes)? $attributes['nb_columns']:1,
                'label' => $this->translator->trans('php_orchestra_backoffice.block.gallery.form.nb_columns')
            ))
            ->add('nb_items', 'text', array(
                'mapped' => false,
                'data' => array_key_exists('nb_items', $attributes)? $attributes['nb_items']:0,
                'label' => $this->translator->trans('php_orchestra_backoffice.block.gallery.form.nb_items')
            ))
            ->add('thumbnail_format', 'choice', array(
                'mapped' => false,
                'choices' => $this->thumbnailConfig,
                'data' => array_key_exists('thumbnail_format', $attributes)? $attributes['thumbnail_format']:1,
                'label' => $this->translator->trans('php_orchestra_backoffice.block.gallery.form.thumbnail_format')
            ))
            ->add('image_format', 'choice', array(
                'mapped' => false,
                'choices' => $this->thumbnailConfig,
                'data' => array_key_exists('image_format', $attributes)? $attributes['image_format']:1,
                'label' => $this->translator->trans('php_orchestra_backoffice.block.gallery.form.image_format')
            ))
            ->add('pictures', 'collection', array(
                'mapped' => false,
                'type' => 'orchestra_media',
                'allow_add' => true,
                'attr' => array(
                    'data-prototype-label-add' => $this->translator->trans('php_orchestra_backoffice.block.gallery.form.media.add'),
                    'data-prototype-label-new' => $this->translator->trans('php_orchestra_backoffice.block.gallery.form.media.new'),
                    'data-prototype-label-remove' => $this->translator->trans('php_orchestra_backoffice.block.gallery.form.media.delete'),
                ),
                'data' => array_key_exists('pictures', $attributes)? $attributes['pictures'] : array(),
                'label' => $this->translator->trans('php_orchestra_backoffice.block.gallery.form.pictures')
            ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gallery';
    }

}
