<?php

namespace OpenOrchestra\BackofficeBundle\DisplayIcon\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ContactStrategy as BaseContactStrategy;

/**
 * Class ContactIconStrategy
 */
class ContactStrategy extends AbstractStrategy
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
        return BaseContactStrategy::NAME == $block;
    }

    /**
     * Display an icon for a block
     *
     * @return string
     */
    public function show()
    {
        return $this->render('OpenOrchestraBackofficeBundle:Block/Contact:showIcon.html.twig');
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'contact';
    }
}
