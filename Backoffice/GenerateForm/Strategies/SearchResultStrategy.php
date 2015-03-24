<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\ModelInterface\Model\BlockInterface;
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
        return $this->getName() === $block->getComponent();
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
        return 'Search_result';
    }

}
