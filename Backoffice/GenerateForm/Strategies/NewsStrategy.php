<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class NewsStrategy
 */
class NewsStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::NEWS === $block->getComponent();
    }

    /**
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function buildForm(FormInterface $form, BlockInterface $block)
    {
        $attributes = $block->getAttributes();

        $form->add('news', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('news', $attributes)? $attributes['news']:'',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'news';
    }

}
