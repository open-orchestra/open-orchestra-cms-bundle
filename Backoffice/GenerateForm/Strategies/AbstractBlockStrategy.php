<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\Backoffice\GenerateForm\GenerateFormInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;

/**
 * Class AbstractBlockStrategy
 */
abstract class AbstractBlockStrategy extends AbstractType implements GenerateFormInterface
{
    /**
     * Get block form template
     * 
     * @return string
     */
    public function getTemplate()
    {
        return 'PHPOrchestraBackofficeBundle:Editorial:template.html.twig';
    }

    /**
     * Get the default configuration for the block
     *
     * @return array
     */
    public function getDefaultConfiguration()
    {
        return array();
    }
}
