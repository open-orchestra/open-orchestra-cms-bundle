<?php

namespace OpenOrchestra\Backoffice\LeftPanel;

use Symfony\Bundle\TwigBundle\TwigEngine;

/**
 * Interface LeftPanelInterface
 */
interface LeftPanelInterface
{
    const EDITORIAL = 'editorial';
    const ADMINISTRATION = 'administration';

    /**
     * @param TwigEngine $templating
     */
    public function setTemplating(TwigEngine $templating);

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
