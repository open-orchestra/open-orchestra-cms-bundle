<?php

namespace OpenOrchestra\MediaAdminBundle\DisplayIcon\Strategies;

use OpenOrchestra\BackofficeBundle\DisplayIcon\Strategies\AbstractStrategy;
use OpenOrchestra\Media\DisplayBlock\Strategies\SlideshowStrategy as BaseSlideshowStrategy;

/**
 * Class SlideshowIconStrategy
 */
class SlideshowStrategy extends AbstractStrategy
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
        return BaseSlideshowStrategy::SLIDESHOW == $block;
    }

    /**
     * Display an icon for a block
     *
     * @return string
     */
    public function show()
    {
        return $this->render('OpenOrchestraMediaAdminBundle:Block/Slideshow:showIcon.html.twig');
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'slideshow';
    }
}
