<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class TreeListCollectionTransformer
 */
class TreeListCollectionTransformer implements DataTransformerInterface
{
    /**
     * @param array $value
     *
     * @return array
     */
    public function transform($value)
    {
        return array('tree_list_collection' => $value);
    }

    /**
     * @param array $value
     *
     * @return array
     */
    public function reverseTransform($value)
    {
        return is_array($value) && array_key_exists('check_list_collection', $value) ? $value['check_list_collection'] : array();
    }
}
