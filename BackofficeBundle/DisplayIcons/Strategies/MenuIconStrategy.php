<?php

namespace PHPOrchestra\BackofficeBundle\DisplayIcons\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MenuIconStrategy
 */
class MenuIconStrategy extends AbstractStrategy
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
        return DisplayBlockInterface::MENU == $block;
    }

    /**
     * Display an icon for a block
     *
     * @return string
     */
    public function show()
    {
        return $this->render('PHPOrchestraBackofficeBundle:Block/Menu:showIcon.html.twig');
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'menu';
    }
}
