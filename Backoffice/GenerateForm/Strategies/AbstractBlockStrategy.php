<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\Backoffice\GenerateForm\GenerateFormInterface;
use Symfony\Component\Form\AbstractType;

/**
 * Class AbstractBlockStrategy
 */
abstract class AbstractBlockStrategy extends AbstractType implements GenerateFormInterface
{
    protected $basicConfigurationBlock;

    /**
     * @param array $basicConfigurationBlock
     */
    public function __construct(array $basicConfigurationBlock)
    {
        $this->basicConfigurationBlock = $basicConfigurationBlock;
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
    public function getMergeDefaultConfiguration()
    {
        return array_merge(
            $this->basicConfigurationBlock,
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
