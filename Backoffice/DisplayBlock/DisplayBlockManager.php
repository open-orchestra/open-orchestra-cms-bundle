<?php

namespace OpenOrchestra\Backoffice\DisplayBlock;

use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class DisplayBlockManager
 */
class DisplayBlockManager
{
    protected $strategies = array();
    protected $templating;
    protected $defaultStrategy;

    /**
     * @param EngineInterface       $templating
     * @param DisplayBlockInterface $defaultStrategy
     */
    public function __construct(
        EngineInterface $templating,
        DisplayBlockInterface $defaultStrategy
    ){
        $this->templating = $templating;

        $this->defaultStrategy = $defaultStrategy;
        $this->defaultStrategy->setManager($this);
    }

    /**
     * @param DisplayBlockInterface $strategy
     */
    public function addStrategy(DisplayBlockInterface $strategy)
    {
        $this->strategies[$strategy->getName()] = $strategy;
        $strategy->setManager($this);
    }

    /**
     * Perform the show action for a block
     *
     * @param ReadBlockInterface $block
     *
     * @return string
     */
    public function show($block)
    {
        /** @var DisplayBlockInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($block)) {
                return $strategy->show($block);
            }
        }

        return $this->defaultStrategy->show($block);
    }

    /**
     * @param ReadBlockInterface $block
     *
     * @return string
     */
    public function toString(ReadBlockInterface $block)
    {
        /** @var DisplayBlockInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($block)) {
                return $strategy->toString($block);
            }
        }

        return $this->defaultStrategy->toString($block);
    }

    /**
     * @return EngineInterface
     */
    public function getTemplating()
    {
        return $this->templating;
    }
}
