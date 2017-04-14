<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

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
        if (!is_array($arrayChoices)) {
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
        if (!is_string($choices) || '' === trim($choices)) {
            return null;
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
