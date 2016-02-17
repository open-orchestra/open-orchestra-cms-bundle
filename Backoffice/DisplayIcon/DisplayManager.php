<?php

namespace OpenOrchestra\Backoffice\DisplayIcon;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class DisplayIconManager
 */
class DisplayManager implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected $strategies = array();

    /**
     * @param DisplayInterface $strategy
     */
    public function addStrategy(DisplayInterface $strategy)
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
        /** @var DisplayInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($block)) {
                return $strategy->show();
            }
        }

        return '';
    }

    /**
     * @return \Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine
     */
    public function getTemplating()
    {
        return $this->container->get('templating');
    }
}
