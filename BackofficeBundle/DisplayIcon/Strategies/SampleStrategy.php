NAME<?php

namespace OpenOrchestra\BackofficeBundle\DisplayIcon\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\SampleStrategy as BaseSampleStrategy;

/**
 * Class SampleIconStrategy
 */
class SampleStrategy extends AbstractStrategy
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
        return BaseSampleStrategy::NAME == $block;
    }

    /**
     * Display an icon for a block
     *
     * @return string
     */
    public function show()
    {
        return $this->render('OpenOrchestraBackofficeBundle:Block/Sample:showIcon.html.twig');
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'sample';
    }
}
