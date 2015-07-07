<?php

namespace OpenOrchestra\BackofficeBundle\Form\DataTransformer;

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
                'label' => $data->getLabel(),
                'class' => $data->getClass(),
                'id' => $data->getId(),
                'maxAge' => $data->getMaxAge(),
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
    }
}
