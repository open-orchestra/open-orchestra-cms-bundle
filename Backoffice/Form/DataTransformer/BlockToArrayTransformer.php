<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class BlockToArrayTransformer
 */
class BlockToArrayTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $data
     *
     * @return array|null
     */
    public function transform($data)
    {
        if ($data instanceof BlockInterface) {
            return array_merge(array(
                'id' => $data->getId(),
                'label' => $data->getLabel(),
                'style' => $data->getStyle(),
                'maxAge' => $data->getMaxAge(),
                'code' => $data->getCode(),
            ), $data->getAttributes());
        }

        return $data;
    }

    /**
     * @param mixed $value
     *
     * @return mixed|void
     */
    public function reverseTransform($value)
    {
        return $value;
    }
}
