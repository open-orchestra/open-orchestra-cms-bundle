<?php

namespace PHPOrchestra\BackofficeBundle\DisplayIcons;

use PHPOrchestra\BackofficeBundle\Exception\DisplayBlockIconNotFoundException;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;

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
     * @throws DisplayBlockIconNotFoundException
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

        throw new DisplayBlockIconNotFoundException($block);
    }

    /**
     * @return \Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine
     */
    public function getTemplating()
    {
        return $this->container->get('templating');
    }
}
