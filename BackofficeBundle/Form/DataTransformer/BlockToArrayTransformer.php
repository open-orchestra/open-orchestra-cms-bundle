<?php

namespace PHPOrchestra\BackofficeBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class BlockToArrayTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $data
     *
     * @return array|null
     */
    public function transform($data)
    {
        if (!empty($data)) {
            return array_merge(array(
                'label' => $data->getLabel(),
                'class' => $data->getClass(),
                'id' => $data->getId()
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