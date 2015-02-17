<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

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
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nodeId');
        $builder->add('nbdoc', 'integer');
        $builder->add('nbspellcheck', 'integer');
        $builder->add('limitField', 'integer');
        $builder->add('fielddisplayed');
        $builder->add('facets');
        $builder->add('filter');
        $builder->add('optionsearch');
        $builder->add('optionsdismax');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'SearchResult';
    }

}
