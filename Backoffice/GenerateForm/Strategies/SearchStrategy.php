<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class SearchStrategy
 */
class SearchStrategy extends AbstractBlockStrategy
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

        $empty = array(
            'value' => '',
            'nodeId' => '',
            'limit' => null,
        );

        $attributes = array_merge($empty, $attributes);

        $form->add('value', 'text', array(
            'mapped' => false,
            'data' => $attributes['value'],
        ));
        $form->add('nodeId', 'text', array(
            'mapped' => false,
            'data' => $attributes['nodeId'],
        ));
        $form->add('limit', 'integer', array(
            'mapped' => false,
            'data' => $attributes['limit'],
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
