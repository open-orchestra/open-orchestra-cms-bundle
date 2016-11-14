<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\UserBundle\DisplayBlock\LoginStrategy as BaseLoginStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class LoginStrategy
 */
class LoginStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return BaseLoginStrategy::LOGIN === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('maxAge', 'hidden', array(
            'group_id' => 'data',
        ));
    }

    /**
     * @return array
     */
    public function getDefaultConfiguration()
    {
        return array(
            'maxAge' => 0
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'login';
    }
}
