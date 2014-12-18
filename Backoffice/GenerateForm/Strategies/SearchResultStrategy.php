<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class SearchResultStrategy
 */
class SearchResultStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::SEARCH_RESULT === $block->getComponent();
    }

    /**
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function buildForm(FormInterface $form, BlockInterface $block)
    {
        $attributes = $block->getAttributes();

        $empty = array(
            'nodeId' => '',
            'nbdoc' => null,
            'nbspellcheck' => null,
            'limitField' => null,
            'fielddisplayed' => '',
            'facets' => '',
            'filter' => '',
            'optionsearch' => '',
            'optionsdismax' => '',
        );
        $attributes = array_merge($empty, $attributes);

        $form->add('nodeId', 'text', array(
            'mapped' => false,
            'data' => $attributes['nodeId'],
        ));
        $form->add('nbdoc', 'integer', array(
            'mapped' => false,
            'data' => $attributes['nbdoc'],
        ));
        $form->add('nbspellcheck', 'integer', array(
            'mapped' => false,
            'data' => $attributes['nbspellcheck'],
        ));
        $form->add('limitField', 'integer', array(
            'mapped' => false,
            'data' => $attributes['limitField'],
        ));
        $form->add('fielddisplayed', 'text', array(
            'mapped' => false,
            'data' => json_encode($attributes['fielddisplayed']),
        ));
        $form->add('facets', 'text', array(
            'mapped' => false,
            'data' => json_encode($attributes['facets']),
        ));
        $form->add('filter', 'text', array(
            'mapped' => false,
            'data' => json_encode($attributes['filter']),
        ));
        $form->add('optionsearch', 'text', array(
            'mapped' => false,
            'data' => json_encode($attributes['optionsearch']),
        ));
        $form->add('optionsdismax', 'text', array(
            'mapped' => false,
            'data' => json_encode($attributes['optionsdismax']),
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'SearchResult';
    }

}
