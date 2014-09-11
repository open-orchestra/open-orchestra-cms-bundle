<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\Backoffice\GenerateForm\GenerateFormInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class AbstractBlockStrategy
 */
abstract class AbstractBlockStrategy implements GenerateFormInterface
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return false;
    }

    /**
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function buildForm(FormInterface $form, BlockInterface $block)
    {
    }

    /**
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function alterFormAfterSubmit(FormInterface $form, BlockInterface $block)
    {
    }

    /**
     * @return string
     */
    public function getName() {
        return 'abstract_block_strategy';
    }
}
