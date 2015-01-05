<?php

namespace PHPOrchestra\BackofficeBundle\StrategyManager;

use PHPOrchestra\Backoffice\LeftPanel\LeftPanelInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class LeftPanelManager
 */
class LeftPanelManager
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
     * @param LeftPanelInterface $strategy
     */
    public function addStrategy(LeftPanelInterface $strategy)
    {
        $this->strategies[$strategy->getParent()][$strategy->getWeight()][$strategy->getName()] = $strategy;
        $strategy->setTemplating($this->container->get('templating'));
    }

    /**
     * @return string
     */
    public function show()
    {
        return $this->container->get('templating')->render('PHPOrchestraBackofficeBundle:BackOffice/Include/LeftPanel:show.html.twig', array(
            'strategies' => $this->strategies
        ));
    }
}
