<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\SampleStrategy as BaseSampleStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SampleStrategy
 */
class SampleStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return BaseSampleStrategy::SAMPLE === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title');
        $builder->add('news', 'textarea');
        $builder->add('author');
        $builder->add('multipleChoice', 'choice', array(
            'choices' => array('foo' => 'foo', 'bar' => 'bar', 'none' => 'none'),
            'multiple' => true,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sample';
    }

}
