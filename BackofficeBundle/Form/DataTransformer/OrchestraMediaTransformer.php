<?php

namespace PHPOrchestra\BackofficeBundle\Form\DataTransformer;

use PHPOrchestra\Media\Model\MediaInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class OrchestraMediaTransformer
 */
class OrchestraMediaTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $value The value in the original representation
     *
     * @return mixed The value in the transformed representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function transform($value)
    {
        if (strpos($value, MediaInterface::MEDIA_PREFIX) === 0) {
            return substr($value, strlen(MediaInterface::MEDIA_PREFIX));
        }

        return $value;
    }

    /**
     * @param mixed $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function reverseTransform($value)
    {
        if ('' != $value && strpos($value, MediaInterface::MEDIA_PREFIX) !== 0) {
            return MediaInterface::MEDIA_PREFIX . $value;
        }

        return $value;
    }
}
