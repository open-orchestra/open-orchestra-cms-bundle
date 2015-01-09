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
        $explode = preg_split('#/|=#', $url);
        $videoId = array_pop($explode);

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
