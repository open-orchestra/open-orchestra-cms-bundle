<?php

namespace OpenOrchestra\Backoffice\LeftPanel;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Interface LeftPanelInterface
 */
interface LeftPanelInterface
{
    const EDITORIAL = 'editorial';
    const ADMINISTRATION = 'administration';

    /**
     * @param EngineInterface $templating
     */
    public function setTemplating(EngineInterface $templating);

    /**
     * @return string
     */
    public function show();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getParent();

    /**
     * @return int
     */
    public function getWeight();

    /**
     * @return string
     */
    public function getRole();
}
