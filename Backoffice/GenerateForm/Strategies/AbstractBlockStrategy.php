<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\Backoffice\GenerateForm\GenerateFormInterface;
use Symfony\Component\Form\AbstractType;

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
        return 'OpenOrchestraBackofficeBundle:Editorial:template.html.twig';
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
