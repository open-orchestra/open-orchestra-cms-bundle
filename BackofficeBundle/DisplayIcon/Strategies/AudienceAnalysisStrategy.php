<?php

namespace OpenOrchestra\BackofficeBundle\DisplayIcon\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\AudienceAnalysisStrategy as BaseAudienceAnalysisStrategy;

/**
 * Class AudienceAnalysisStrategy
 */
class AudienceAnalysisStrategy extends AbstractStrategy
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
        return BaseAudienceAnalysisStrategy::NAME == $block;
    }

    /**
     * Display an icon for a block
     *
     * @return string
     */
    public function show()
    {
        return $this->render('OpenOrchestraBackofficeBundle:Block/AudienceAnalysis:showIcon.html.twig');
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'audience_analysis';
    }
}
