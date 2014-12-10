<?php

namespace PHPOrchestra\BackofficeBundle\DisplayIcons\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use Symfony\Component\HttpFoundation\Response;

class FooterIconStrategy extends AbstractStrategy
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
        return DisplayBlockInterface::FOOTER == $block;
    }

    /**
     * Display an icon for a block
     *
     * @return Response
     */
    public function show()
    {
        return $this->render('PHPOrchestraBackofficeBundle:Block/Footer:showIcon.html.twig');
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'footer';
    }
}
