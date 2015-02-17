<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
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
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pictures', 'collection', array(
            'type' => 'orchestra_media',
            'allow_add' => true,
            'attr' => array(
                'data-prototype-label-add' => $this->translator->trans('php_orchestra_backoffice.block.carrousel.form.media.add'),
                'data-prototype-label-new' => $this->translator->trans('php_orchestra_backoffice.block.carrousel.form.media.new'),
                'data-prototype-label-remove' => $this->translator->trans('php_orchestra_backoffice.block.carrousel.form.media.delete'),
            ),
        ));
        $builder->add('width');
        $builder->add('height');
        $builder->add('carousel_id');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'carrousel';
    }

}
