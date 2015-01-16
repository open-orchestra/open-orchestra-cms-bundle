<?php

namespace PHPOrchestra\BackofficeBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class StringToBooleanTransformer
 */
class StringToBooleanTransformer implements DataTransformerInterface
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
        if (is_bool($value)) {
            return $value;
        }

        if (empty($value)) {
            return false;
        }

        if (1 === $value) {
            return true;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Expected a string.');
        }

        return true;
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
        if (false == $value) {
            return '0';
        }

        if (1 === $value) {
            return '1';
        }

        if (!is_bool($value)) {
            throw new TransformationFailedException('Expected a Boolean.');
        }

        return '1';
    }


} 