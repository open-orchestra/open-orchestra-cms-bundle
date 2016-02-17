<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

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
        if (is_scalar($dataChoice)) {
            return (string) $dataChoice;
        } elseif (is_array($dataChoice) && $dataChoice !== array()) {
            return current($dataChoice);
        }

        return "";
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
