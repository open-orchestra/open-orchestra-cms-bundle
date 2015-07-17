<?php

namespace OpenOrchestra\BackofficeBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class ChoiceArrayToStringTransformer
 */
class ChoiceArrayToStringTransformer implements DataTransformerInterface
{
    /**
     * @param array|string $dataChoice
     *
     * @return string
     */
    public function transform($dataChoice)
    {
        if (null === $dataChoice || $dataChoice === array()) {
            return "";
        }

        if (is_scalar($dataChoice)) {
            return $dataChoice;
        } else {
            return current($dataChoice);
        }
    }

    /**
     * @param string $dataChoice
     *
     * @return string
     */
    public function reverseTransform($dataChoice)
    {
        return $dataChoice;
    }
}
