<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\LanguageListStrategy as BaseLanguageListStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class LanguageChoiceStrategy
 */
class LanguageListStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return BaseLanguageListStrategy::NAME === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'language_list';
    }
}
