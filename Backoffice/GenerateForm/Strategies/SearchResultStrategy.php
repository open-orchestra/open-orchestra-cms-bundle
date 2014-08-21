<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\Backoffice\GenerateForm\GenerateFormInterface;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class SearchResultStrategy
 */
class SearchResultStrategy implements GenerateFormInterface
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

        $form->add('nodeId', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('nodeId', $attributes)? $attributes['nodeId']: '',
        ));
        $form->add('nbdoc', 'integer', array(
            'mapped' => false,
            'data' => array_key_exists('nbdoc', $attributes)? $attributes['nbdoc']: null,
        ));
        $form->add('nbspellcheck', 'integer', array(
            'mapped' => false,
            'data' => array_key_exists('nbspellcheck', $attributes)? $attributes['nbspellcheck']: null,
        ));
        $form->add('limitField', 'integer', array(
            'mapped' => false,
            'data' => array_key_exists('limitField', $attributes)? $attributes['limitField']: null,
        ));
        $form->add('fielddisplayed', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('fielddisplayed', $attributes)? json_encode($attributes['fielddisplayed']): '',
        ));
        $form->add('facets', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('facets', $attributes)? json_encode($attributes['facets']): '',
        ));
        $form->add('filter', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('filter', $attributes)? json_encode($attributes['filter']): '',
        ));
        $form->add('optionsearch', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('optionsearch', $attributes)? json_encode($attributes['optionsearch']): '',
        ));
        $form->add('optionsdismax', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('optionsdismax', $attributes)? json_encode($attributes['optionsdismax']): '',
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
