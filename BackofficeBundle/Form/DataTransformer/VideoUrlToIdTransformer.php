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
        return $url;
    }

    /**
     * @param string $videoId
     *
     * @return string
     */
    public function reverseTransform($videoId)
    {
        $explode = preg_split('#/|=#', $videoId);
        $videoId = array_pop($explode);

        return $videoId;
    }
}
