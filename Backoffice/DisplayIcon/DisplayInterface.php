<?php

namespace OpenOrchestra\Backoffice\DisplayIcon;

/**
 * Interface DisplayIconInterface
 */
interface DisplayInterface
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
     * @param DisplayManager $manager
     */
    public function setManager(DisplayManager $manager);
}
