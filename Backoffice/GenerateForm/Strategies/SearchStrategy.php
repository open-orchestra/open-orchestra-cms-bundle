<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\Backoffice\GenerateForm\GenerateFormInterface;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class SearchStrategy
 */
class SearchStrategy implements GenerateFormInterface
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::SEARCH === $block->getComponent();
    }

    /**
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function buildForm(FormInterface $form, BlockInterface $block)
    {
        $attributes = $block->getAttributes();

        $form->add('value', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('value', $attributes)? $attributes['value']: '',
        ));
        $form->add('class', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('class', $attributes)? $attributes['class']: '',
        ));
        $form->add('nodeId', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('nodeId', $attributes)? $attributes['nodeId']: '',
        ));
        $form->add('limit', 'integer', array(
            'mapped' => false,
            'data' => array_key_exists('limit', $attributes)? $attributes['limit']: null,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'search';
    }

}
