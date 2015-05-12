<?php

namespace OpenOrchestra\BackofficeBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class ChoicesOptionToArrayTransformer
 */
class ChoicesOptionToArrayTransformer implements DataTransformerInterface
{
    protected $suppressSpecialCharacterClass;

    /**
     * @param SuppressSpecialCharacterTransformer $suppressSpecialCharacterClass
     */
    public function __construct(SuppressSpecialCharacterTransformer $suppressSpecialCharacterClass)
    {
        $this->suppressSpecialCharacterClass = $suppressSpecialCharacterClass;
    }

    /**
     * @param array $arrayChoices
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
     * @return array choix1,choix2,choix3
     */
    public function reverseTransform($choices)
    {
        if (null === $choices || '' === trim($choices)) {
            return array();
        }

        $choices = explode(',', $choices);
        $arrayChoice = array();

        foreach ($choices as $choice) {
            $choice = $this->suppressSpecialCharacterClass->transform($choice);
            if ('' != $choice) {
                $arrayChoice[$choice] = $choice;
            }
        }

        return $arrayChoice;
    }
}
