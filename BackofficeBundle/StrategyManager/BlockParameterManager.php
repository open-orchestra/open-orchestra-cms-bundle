<?php

namespace PHPOrchestra\BackofficeBundle\StrategyManager;

use PHPOrchestra\Backoffice\BlockParameter\BlockParameterInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;


/**
 * Class BlockParameterManager
 */
class BlockParameterManager
{
    protected $strategies = array();

    /**
     * @param BlockParameterInterface $strategy
     */
    public function addStrategy(BlockParameterInterface $strategy)
    {
        $this->strategies[$strategy->getName()] = $strategy;
    }

    /**
     * @param BlockInterface $block
     *
     * @return array
     */
    public function getBlockParameter(BlockInterface $block)
    {
        /** @var BlockParameterInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($block)) {
                return $strategy->getBlockParameter();
            }
        }

        return array();
    }
}
