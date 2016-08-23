<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\Backoffice\GenerateForm\GenerateFormInterface;
use Symfony\Component\Form\AbstractType;

/**
 * Class AbstractBlockStrategy
 */
abstract class AbstractBlockStrategy extends AbstractType implements GenerateFormInterface
{
    protected $basicBlockConfiguration;

    /**
     * @param array $basicBlockConfiguration
     */
    public function __construct(array $basicBlockConfiguration)
    {
        $this->basicBlockConfiguration = $basicBlockConfiguration;
    }

    /**
     * Get block form template
     *
     * @return string
     */
    public function getTemplate()
    {
        return 'OpenOrchestraBackofficeBundle::form.html.twig';
    }

    /**
     * Get merge the default configuration and basic configuration for the block
     *
     * @return array
     */
    public function getMergedDefaultConfiguration()
    {
        return array_merge(
            $this->basicBlockConfiguration,
            $this->getDefaultConfiguration()
        );
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

    /**
     * Get the required Uri parameters
     *
     * @return array
     */
    public function getRequiredUriParameter()
    {
        return array();
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'oo_block';
    }
}
