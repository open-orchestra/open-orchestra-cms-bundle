<?php

namespace OpenOrchestra\BackofficeBundle\StrategyManager;

use OpenOrchestra\Backoffice\LeftPanel\LeftPanelInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Class LeftPanelManager
 */
class LeftPanelManager
{
    protected $strategies = array();
    protected $templateEngine;

    /**
     * @param EngineInterface $templateEngine
     */
    public function __construct(EngineInterface $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    /**
     * @param LeftPanelInterface $strategy
     */
    public function addStrategy(LeftPanelInterface $strategy)
    {
        $this->strategies[$strategy->getParent()][$strategy->getWeight()][$strategy->getName()] = $strategy;
        $strategy->setTemplating($this->templateEngine);
    }

    /**
     * @return string
     */
    public function show()
    {
        return $this->templateEngine->render('OpenOrchestraBackofficeBundle:BackOffice/Include/LeftPanel:show.html.twig', array(
            'strategies' => $this->strategies
        ));
    }
}
