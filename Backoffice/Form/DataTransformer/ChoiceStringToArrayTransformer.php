<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
@trigger_error('The '.__NAMESPACE__.'\ChoiceArrayToStringTransformer class is deprecated since version 1.1.0 and will be removed in 1.2.0', E_USER_DEPRECATED);

/**
 * Class ChoiceStringToArrayTransformer
 *
 * @deprecated will be removed in 1.2.0
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
        }

        return array();
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
