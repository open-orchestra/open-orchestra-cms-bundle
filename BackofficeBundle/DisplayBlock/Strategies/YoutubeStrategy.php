<?php

namespace PHPOrchestra\BackofficeBundle\DisplayBlock\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class YoutubeStrategy
 */
class YoutubeStrategy extends AbstractStrategy
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
        return DisplayBlockInterface::YOUTUBE === $block->getComponent();
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
        if (array_key_exists('showinfo', $attributes) && $attributes['showinfo'] == true) {
            $urlParams['showinfo'] = 1;
        }
        if (array_key_exists('fs', $attributes) && $attributes['fs'] == true) {
            $urlParams['fs'] = 1;
        }
        if (array_key_exists('rel', $attributes) && $attributes['rel'] == true) {
            $urlParams['rel'] = 1;
        }
        if (array_key_exists('disablekb', $attributes) && $attributes['disablekb'] == true) {
            $urlParams['disablekb'] = 1;
        }
        if (array_key_exists('loop', $attributes) && $attributes['loop'] == true) {
            $urlParams['loop'] = 1;
        }
        if (!array_key_exists('controls', $attributes) || $attributes['controls'] == false) {
            $urlParams['controls'] = 0;
        }
        if (array_key_exists('theme', $attributes) && $attributes['theme'] == true) {
            $urlParams['theme'] = 'light';
        }
        if (array_key_exists('color', $attributes) && $attributes['color'] == true) {
            $urlParams['color'] = 'white';
        }
        if ($attributes['hl'] !== '') {
            $urlParams['hl'] = $attributes['hl'];
        }

        $url = "//www.youtube.com/embed/" . $attributes['videoId'] ."?" . http_build_query($urlParams, '', '&amp;');

        $parameters = array(
            'url' => $url,
            'width' => $attributes['width'],
            'height' => $attributes['height']
        );

        return $this->render('PHPOrchestraBackofficeBundle:Block/Youtube:show.html.twig', $parameters);
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'youtube';
    }
}
