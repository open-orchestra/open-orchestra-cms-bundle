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

        $initialize = array(
            'autoplay' => false,
            'showinfo' => false,
            'fs' => false,
            'rel' => false,
            'disablekb' => false,
            'loop' => false,
            'controls' => false,
            'theme' => false,
            'color' => false,
        );

        $attributes = array_merge($initialize, $attributes);

        $urlParams = array();
        foreach (array('autoplay', 'showinfo', 'fs', 'rel', 'disablekb', 'loop') as $key) {
            if ($attributes[$key] === true) {
                $urlParams[$key] = 1;
            }
        }

        if ($attributes['controls'] === false) {
            $urlParams['controls'] = 0;
        }
        if ($attributes['theme'] === true) {
            $urlParams['theme'] = 'light';
        }
        if ($attributes['color'] === true) {
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
