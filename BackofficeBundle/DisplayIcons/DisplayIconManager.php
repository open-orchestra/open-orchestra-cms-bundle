<?php

namespace PHPOrchestra\BackofficeBundle\DisplayIcons;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class DisplayIconManager
 */
class DisplayIconManager
{
    protected $strategies = array();
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param DisplayIconInterface $strategy
     */
    public function addStrategy(DisplayIconInterface $strategy)
    {
        $this->strategies[$strategy->getName()] = $strategy;
        $strategy->setManager($this);
    }

    /**
     * Perform the show action for a block
     *
     * @param string $block
     *
     * @return string
     */
    public function show($block)
    {
        /** @var DisplayIconInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($block)) {
                return $strategy->show();
            }
        }
    }

    /**
     * @return \Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine
     */
    public function getTemplating()
    {
        return $this->container->get('templating');
    }
}
