<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class CarrouselStrategy
 */
class CarrouselStrategy extends AbstractBlockStrategy
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
        return DisplayBlockInterface::CARROUSEL === $block->getComponent();
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
                'data-prototype-label-add' => $this->translator->trans('php_orchestra_backoffice.block.carrousel.form.media.add'),
                'data-prototype-label-new' => $this->translator->trans('php_orchestra_backoffice.block.carrousel.form.media.new'),
                'data-prototype-label-remove' => $this->translator->trans('php_orchestra_backoffice.block.carrousel.form.media.delete'),
            ),
            'data' => array_key_exists('pictures', $attributes)? $attributes['pictures'] : array()
        ));
        $form->add('width', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('width', $attributes)? $attributes['width']:'',
        ));
        $form->add('height', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('height', $attributes)? $attributes['height']:'',
        ));
        $form->add('carousel_id', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('carousel_id', $attributes)? $attributes['carousel_id']:'',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'carrousel';
    }

}
