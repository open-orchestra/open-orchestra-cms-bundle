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
        $videoId = array_pop(preg_split('#/|=#', $url));

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
