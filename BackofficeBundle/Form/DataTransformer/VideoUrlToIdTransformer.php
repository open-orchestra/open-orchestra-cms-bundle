<?php

namespace PHPOrchestra\BackofficeBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class VideoUrlToId
 */
class VideoUrlToIdTransformer implements DataTransformerInterface
{
    /**
     * @param string $url
     *
     * @return string
     */
    public function transform($url)
    {
        $explode = explode("/", $url);
        $videoId = $explode[0];

        if (count($explode) > 1) {
            $videoId = array_pop($explode);

            if (count(explode('=', $videoId))) {
                $videoId = array_pop(explode('=', $videoId));
            }
        }

        return $videoId;
    }

    /**
     * @param string $videoId
     *
     * @return string
     */
    public function reverseTransform($videoId)
    {
        return $videoId;
    }
}
