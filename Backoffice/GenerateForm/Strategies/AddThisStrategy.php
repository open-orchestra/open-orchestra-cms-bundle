<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\AddThisStrategy as BaseAddThisStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AddThisStrategy
 */
class AddThisStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return BaseAddThisStrategy::NAME === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pubid', 'text', array(
            'constraints' => new NotBlank(),
        ));
        $builder->add('addThisClass', 'textarea', array(
            'constraints' => new NotBlank(),
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'add_this';
    }
}
