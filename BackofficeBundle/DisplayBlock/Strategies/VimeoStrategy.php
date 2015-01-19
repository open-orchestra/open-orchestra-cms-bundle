<?php

namespace PHPOrchestra\BackofficeBundle\DisplayBlock\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VimeoStrategy
 */
class VimeoStrategy extends AbstractStrategy
{
    /**
     * Check if the strategy support this block
     *
     * @param BlockInterface $block
     *
     * @return boolean
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::VIMEO === $block->getComponent();
    }

    /**
     * Perform the show action for a block
     *
     * @param BlockInterface $block
     *
     * @return Response
     */
    public function show(BlockInterface $block)
    {
        $attributes = $block->getAttributes();

        $urlParams = array();
        if (array_key_exists('autoplay', $attributes) && $attributes['autoplay'] == true) {
            $urlParams['autoplay'] = 1;
        }
        if (!array_key_exists('title', $attributes) || $attributes['title'] == false) {
            $urlParams['title'] = 0;
        }
        if (array_key_exists('fullscreen', $attributes) && $attributes['fullscreen'] == true) {
            $urlParams['fullscreen'] = 1;
        }
        if (!array_key_exists('byline', $attributes) || $attributes['byline'] == false) {
            $urlParams['byline'] = 0;
        }
        if (!array_key_exists('portrait', $attributes) || $attributes['portrait'] == false) {
            $urlParams['portrait'] = 0;
        }
        if (array_key_exists('loop', $attributes) && $attributes['loop'] == true) {
            $urlParams['loop'] = 1;
        }
        if (!array_key_exists('badge', $attributes) || $attributes['badge'] == false) {
            $urlParams['badge'] = 0;
        }
        if ($attributes['color'] !== '') {
            $urlParams['color'] = $attributes['color'];
        }

        $url = "//player.vimeo.com/video/" . $attributes['videoId'] ."?" . http_build_query($urlParams, '', '&amp;');

        $parameters = array(
            'url' => $url,
            'width' => $attributes['width'],
            'height' => $attributes['height']
        );

        return $this->render('PHPOrchestraBackofficeBundle:Block/Vimeo:show.html.twig', $parameters);
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'vimeo';
    }
}
