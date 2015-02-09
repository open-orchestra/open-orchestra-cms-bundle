<?php

namespace PHPOrchestra\BackofficeBundle\DisplayIcon\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;

/**
 * Class MediaListIconStrategy
 */
class MediaListStrategy extends AbstractStrategy
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
        return DisplayBlockInterface::MEDIA_LIST_BY_KEYWORD == $block;
    }

    /**
     * Display an icon for a block
     *
     * @return string
     */
    public function show()
    {
        return $this->render('PHPOrchestraBackofficeBundle:Block/MediaList:showIcon.html.twig');
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'media_list';
    }
}
