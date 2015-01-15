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

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
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

        $form->add('pictures', 'collection', array(
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
        $form->add('nb_columns', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('nb_columns', $attributes)? $attributes['nb_columns']:1,
            'label' => $this->translator->trans('php_orchestra_backoffice.block.gallery.form.nb_columns')
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
