<?php

namespace OpenOrchestra\BackofficeBundle\DisplayIcon\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\LanguageListStrategy as BaseLanguageListStrategy;

/**
 * Class LanguageListIconStrategy
 */
class LanguageListStrategy extends AbstractStrategy
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
        return BaseLanguageListStrategy::LANGUAGE_LIST == $block;
    }

    /**
     * Display an icon for a block
     *
     * @return string
     */
    public function show()
    {
        return $this->render('OpenOrchestraBackofficeBundle:Block/LanguageList:showIcon.html.twig');
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'language_list';
    }
}
