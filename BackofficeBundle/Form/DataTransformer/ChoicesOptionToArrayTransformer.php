<?php

namespace OpenOrchestra\BackofficeBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class ChoicesOptionToArrayTransformer
 */
class ChoicesOptionToArrayTransformer implements DataTransformerInterface
{
    /**
     * @param array $arrayChoices
     *
     * @return string
     */
    public function transform($arrayChoices)
    {
        if (null === $arrayChoices) {
            return "";
        }

        return implode(',', $arrayChoices);
    }

    /**
     * @param string $choices
     *
     * @return array
     */
    public function reverseTransform($choices)
    {
        if (null === $choices || '' === trim($choices)) {
            return array();
        }

        $choices = explode(',', $choices);
        $arrayChoice = array();

        foreach ($choices as $choice) {
            $choice = $this->suppressSpecialCharacterTranslateChoice($choice);
            if ('' != $choice) {
                $arrayChoice[$choice] = $choice;
            }
        }

        return $arrayChoice;
    }

    /**
     * @param string $choice
     *
     * @return string
     */
    protected function suppressSpecialCharacterTranslateChoice($choice)
    {
        $caracteres = array(
            'À' => 'a', 'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', 'È' => 'e',
            'É' => 'e', 'Ê' => 'e', 'Ë' => 'e', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'Ì' => 'i', 'Í' => 'i',
            'Î' => 'i', 'Ï' => 'i', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o',
            'Ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o', 'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'Œ' => 'oe', 'œ' => 'oe'
        );

        $choice = strtr($choice, $caracteres);
        $choice = preg_replace('#[^0-9a-z_.]+#i', '', $choice);

        return $choice;
    }


}
