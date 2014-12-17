<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

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
        return DisplayBlockInterface::SAMPLE === $block->getComponent();
    }

    /**
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function buildForm(FormInterface $form, BlockInterface $block)
    {
        $attributes = $block->getAttributes();

        $form->add('title', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('title', $attributes)? $attributes['title']: '',
        ));
        $form->add('news', 'textarea', array(
            'mapped' => false,
            'data' => array_key_exists('news', $attributes)? $attributes['news']: '',
        ));
        $form->add('author', 'text', array(
            'mapped' => false,
            'data' => array_key_exists('author', $attributes)? $attributes['author']: '',
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
