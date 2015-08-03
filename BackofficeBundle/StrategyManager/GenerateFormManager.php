<?php

namespace OpenOrchestra\BackofficeBundle\StrategyManager;

use OpenOrchestra\Backoffice\Exception\MissingGenerateFormStrategyException;
use OpenOrchestra\Backoffice\GenerateForm\GenerateFormInterface;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

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
     * @param FormBuilderInterface  $form
     * @param array                 $options
     * @param BlockInterface        $block
     *
     * @deprecated remove in tag 0.4.0
     */
    public function buildForm(FormBuilderInterface $form, array $options, BlockInterface $block)
    {
        /** @var GenerateFormInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($block)) {
                $strategy->buildForm($form, $options);
            }
        }
    }

    /**
     * Get the default configuration for the block
     *
     * @param BlockInterface $block
     *
     * @return array
     */
    public function getDefaultConfiguration(BlockInterface $block)
    {
        /** @var GenerateFormInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($block)) {
                return $strategy->getDefaultConfiguration();
            }
        }

        throw new MissingGenerateFormStrategyException();
    }

    /**
     * Get the required Uri parameters for the block
     *
     * @param BlockInterface $block
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
     */
    public function createForm(BlockInterface $block)
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
    }
}
