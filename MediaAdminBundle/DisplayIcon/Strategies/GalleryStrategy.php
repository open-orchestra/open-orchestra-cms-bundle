<?php

namespace OpenOrchestra\MediaAdminBundle\DisplayIcon\Strategies;

use OpenOrchestra\BackofficeBundle\DisplayIcon\Strategies\AbstractStrategy;
use OpenOrchestra\Media\DisplayBlock\Strategies\GalleryStrategy as BaseGalleryStrategy;

/**
 * Class GalleryIconStrategy
 */
class GalleryStrategy extends AbstractStrategy
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
        return BaseGalleryStrategy::GALLERY == $block;
    }

    /**
     * Display an icon for a block
     *
     * @return string
     */
    public function show()
    {
        return $this->render('OpenOrchestraMediaAdminBundle:Block/Gallery:showIcon.html.twig');
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'gallery';
    }
}
