<?php

namespace OpenOrchestra\BackofficeBundle\Form\DataTransformer;

use OpenOrchestra\ModelInterface\Helper\SuppressSpecialCharacterHelperInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class ChoicesOptionToArrayTransformer
 */
class ChoicesOptionToArrayTransformer implements DataTransformerInterface
{
    protected $suppressSpecialCharacterHelper;

    /**
     * @param SuppressSpecialCharacterHelperInterface $suppressSpecialCharacterHelper
     */
    public function __construct(SuppressSpecialCharacterHelperInterface $suppressSpecialCharacterHelper)
    {
        $this->suppressSpecialCharacterHelper = $suppressSpecialCharacterHelper;
    }

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
            $choice = $this->suppressSpecialCharacterHelper->transform($choice, array('_','.'));
            if ('' != $choice) {
                $arrayChoice[$choice] = $choice;
            }
        }

        return $arrayChoice;
    }
}
