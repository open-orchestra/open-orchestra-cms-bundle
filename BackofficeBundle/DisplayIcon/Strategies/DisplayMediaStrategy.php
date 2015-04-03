<?php

namespace OpenOrchestra\BackofficeBundle\DisplayIcon\Strategies;

use OpenOrchestra\MediaBundle\DisplayBlock\Strategies\DisplayMediaStrategy as BaseMediaStrategy;

/**
 * Class DisplayMediaStrategy
 */
class DisplayMediaStrategy extends AbstractStrategy
{
    /**
     * Check if the strategy support this block
     *
     * @param string $block
     *
     * @return boolean
     */
    public function support($block)
    {
        return BaseMediaStrategy::DISPLAY_MEDIA == $block;
    }

    /**
     * Perform the show action for a block
     *
     * @return string
     */
    public function show()
    {
        return $this->render('OpenOrchestraBackofficeBundle:Block/DisplayMedia:showIcon.html.twig');
    }


    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'display_media';
    }

}
