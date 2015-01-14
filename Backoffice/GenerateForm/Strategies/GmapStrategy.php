<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class GmapStrategy
 */
class GmapStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::GMAP === $block->getComponent();
    }

    /**
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function buildForm(FormInterface $form, BlockInterface $block)
    {
        $attributes = $block->getAttributes();

        $empty = array(
            'latitude' => '',
            'longitude' => '',
            'zoom' => '',
        );

        $attributes = array_merge($empty, $attributes);

        $form->add('latitude', 'text', array(
            'mapped' => false,
            'data' => $attributes['latitude'],
        ));
        $form->add('longitude', 'text', array(
            'mapped' => false,
            'data' => $attributes['longitude'],
        ));
        $form->add('zoom', 'text', array(
            'mapped' => false,
            'data' => $attributes['zoom'],
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gmap';
    }
}
