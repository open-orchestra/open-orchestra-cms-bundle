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

        $initialize = array(
            'autoplay' => false,
            'info' => false,
            'logo' => false,
            'related' => false,
            'chromeless' => false,
        );

        $attributes = array_merge($initialize, $attributes);

        $urlParams = array();

        foreach (array('autoplay', 'chromeless') as $key) {
            if ($attributes[$key] === true) {
                $urlParams[$key] = 1;
            }
        }
        foreach (array('logo', 'info', 'related') as $key) {
            if ($attributes[$key] === false) {
                $urlParams[$key] = 0;
            }
        }
        foreach (array('background', 'foreground', 'highlight', 'quality') as $key) {
            if ($attributes[$key] !== '') {
                $urlParams[$key] = $attributes[$key];
            }
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
