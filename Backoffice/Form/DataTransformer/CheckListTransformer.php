<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class CheckListTransformer
 */
class CheckListTransformer implements DataTransformerInterface
{
    /**
     * @param array $value
     *
     * @return array
     */
    public function transform($value)
    {
        return array('check_list' => $value);
    }

    /**
     * @param array $value
     *
     * @return array
     */
    public function reverseTransform($value)
    {
        return is_array($value) && array_key_exists('check_list', $value) ? $value['check_list'] : array();
    }
}
