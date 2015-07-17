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
        if (null === $dataChoice || '' === $dataChoice) {
            return array();
        }

        if (is_array($dataChoice)) {
            return $dataChoice;
        } else {
            return array($dataChoice);
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
