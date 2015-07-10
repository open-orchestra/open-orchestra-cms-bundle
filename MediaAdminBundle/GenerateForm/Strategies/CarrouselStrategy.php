<?php

namespace OpenOrchestra\MediaAdminBundle\GenerateForm\Strategies;

use OpenOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use OpenOrchestra\Media\DisplayBlock\Strategies\CarrouselStrategy as BaseCarrouselStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

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
        return BaseCarrouselStrategy::CARROUSEL === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'text', array(
            'label' => 'open_orchestra_backoffice.form.block.id',
            'constraints' => new NotBlank(),
        ));
        $builder->add('pictures', 'collection', array(
            'type' => 'orchestra_media',
            'constraints' => new NotBlank(),
            'allow_add' => true,
            'attr' => array(
                'data-prototype-label-add' => $this->translator->trans('open_orchestra_media_admin.block.carrousel.form.media.add'),
                'data-prototype-label-new' => $this->translator->trans('open_orchestra_media_admin.block.carrousel.form.media.new'),
                'data-prototype-label-remove' => $this->translator->trans('open_orchestra_media_admin.block.carrousel.form.media.delete'),
            ),
        ));
        $builder->add('width', 'text', array(
            'constraints' => new NotBlank(),
        ));
        $builder->add('height', 'text', array(
            'constraints' => new NotBlank(),
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
