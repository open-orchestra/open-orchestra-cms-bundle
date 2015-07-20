<?php

namespace OpenOrchestra\BackofficeBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class ChoiceStringToArrayTransformer
 */
class ChoiceStringToArrayTransformer implements DataTransformerInterface
{
    /**
     * @param array|string $dataChoice
     *
     * @return array
     */
    public function transform($dataChoice)
    {
        if (is_scalar($dataChoice) && $dataChoice !== '') {
            return array((string)$dataChoice);
        } elseif (is_array($dataChoice)) {
            return $dataChoice;
        } else {
            return array();
        }
    }

    /**
     * @param array $dataChoice
     *
     * @return array
     */
    public function reverseTransform($dataChoice)
    {
        return $dataChoice;
    }
}
