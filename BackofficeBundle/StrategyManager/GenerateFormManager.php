<?php

namespace OpenOrchestra\BackofficeBundle\StrategyManager;

use OpenOrchestra\Backoffice\Exception\MissingGenerateFormStrategyException;
use OpenOrchestra\Backoffice\GenerateForm\GenerateFormInterface;
use OpenOrchestra\ModelInterface\Model\BlockInterface;

/**
 * Class GenerateFormManager
 */
class GenerateFormManager
{
    protected $strategies = array();

    /**
     * @param GenerateFormInterface $strategy
     */
    public function addStrategy(GenerateFormInterface $strategy)
    {
        $this->strategies[$strategy->getName()] = $strategy;
    }

    /**
     * Get the default configuration for the block
     *
     * @param BlockInterface $block
     *
     * @throws MissingGenerateFormStrategyException
     *
     * @return array
     */
    public function getDefaultConfiguration(BlockInterface $block)
    {
        /** @var GenerateFormInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($block)) {
                return $strategy->getMergedDefaultConfiguration();
            }
        }

        throw new MissingGenerateFormStrategyException();
    }

    /**
     * Get the required Uri parameters for the block
     *
     * @param BlockInterface $block
     *
     * @throws MissingGenerateFormStrategyException
     *
     * @return array
     */
    public function getRequiredUriParameter(BlockInterface $block)
    {
        /** @var GenerateFormInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($block)) {
                return $strategy->getRequiredUriParameter();
            }
        }

        throw new MissingGenerateFormStrategyException();
    }

    /**
     * @param BlockInterface $block
     *
     * @throws MissingGenerateFormStrategyException
     *
     * @return GenerateFormInterface
     *
     * @deprecated  deprecated since version 1.2.0 and will be removed in version 1.3.0, use getFormType
     */
    public function createForm(BlockInterface $block)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 1.3.0. Use the '.__CLASS__.'::getFormType method instead.', E_USER_DEPRECATED);
        /** @var GenerateFormInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($block)) {
                return $strategy;
            }
        }

        throw new MissingGenerateFormStrategyException();
    }

    /**
     * @param BlockInterface $block
     *
     * @throws MissingGenerateFormStrategyException
     *
     * @return GenerateFormInterface
     */
    public function getFormType(BlockInterface $block)
    {
        /** @var GenerateFormInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($block)) {
                return $strategy;
            }
        }

        throw new MissingGenerateFormStrategyException();
    }

    /**
     * @param BlockInterface $block
     *
     * @throws MissingGenerateFormStrategyException
     *
     * @return string
     */
    public function getTemplate(BlockInterface $block)
    {
        /** @var GenerateFormInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($block)) {
                return $strategy->getTemplate();
            }
        }

        throw new MissingGenerateFormStrategyException();
    }
}
