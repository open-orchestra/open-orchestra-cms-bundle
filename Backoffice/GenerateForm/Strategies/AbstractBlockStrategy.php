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
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function alterFormAfterSubmit(FormInterface $form, BlockInterface $block)
    {
    }
}
