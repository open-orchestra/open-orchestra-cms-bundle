<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
@trigger_error('The '.__NAMESPACE__.'\ChoiceArrayToStringTransformer class is deprecated since version 1.1.0 and will be removed in 1.2.0', E_USER_DEPRECATED);

/**
 * Class ChoiceArrayToStringTransformer
 *
 * @deprecated will be removed in 1.2.0
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
