<?php

namespace PHPOrchestra\BackofficeBundle\DisplayIcons;

use Symfony\Component\HttpFoundation\Response;

interface DisplayIconInterface
{
    /**
     * Check if the strategy support this block
     *
     * @param string $block
     *
     * @return boolean
     */
    public function support($block);

    /**
     * Perform the show action for a block
     *
     * @return string
     */
    public function show();

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName();

    /**
     * Set the manager
     *
     * @param DisplayIconManager $manager
     */
    public function setManager(DisplayIconManager $manager);
} 