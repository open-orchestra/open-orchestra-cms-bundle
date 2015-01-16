<?php

namespace PHPOrchestra\BackofficeBundle\DisplayBlock\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DailymotionStrategy
 */
class DailymotionStrategy extends AbstractStrategy
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
        return DisplayBlockInterface::DAILYMOTION === $block->getComponent();
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
        if (!array_key_exists('info', $attributes) || $attributes['info'] == false) {
            $urlParams['info'] = 0;
        }
        if (!array_key_exists('logo', $attributes) || $attributes['logo'] == false) {
            $urlParams['logo'] = 0;
        }
        if (!array_key_exists('related', $attributes) || $attributes['related'] == false) {
            $urlParams['related'] = 0;
        }
        if (array_key_exists('chromeless', $attributes) && $attributes['chromeless'] == true) {
            $urlParams['chromeless'] = 1;
        }
        if ($attributes['background'] !== '') {
            $urlParams['background'] = $attributes['background'];
        }
        if ($attributes['foreground'] !== '') {
            $urlParams['foreground'] = $attributes['foreground'];
        }
        if ($attributes['highlight'] !== '') {
            $urlParams['highlight'] = $attributes['highlight'];
        }
        if ($attributes['quality'] !== '') {
            $urlParams['quality'] = $attributes['quality'];
        }

        $url = "//www.dailymotion.com/embed/video/" . $attributes['videoId'] . "?" . http_build_query($urlParams, '', '&amp;');

        $parameters = array(
            'url' => $url,
            'width' => $attributes['width'],
            'height' => $attributes['height']
        );

        return $this->render('PHPOrchestraBackofficeBundle:Block/Dailymotion:show.html.twig', $parameters);
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'dailymotion';
    }
}
