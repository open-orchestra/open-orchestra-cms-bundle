<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class CollectionTransformer
 */
class CollectionTransformer implements DataTransformerInterface
{
    protected $name;

    /**
     * @param string $name
     */
    public function __construct(
        $name
    ) {
        $this->name = $name;
    }



    /**
     * @param array $value
     *
     * @return array
     */
    public function transform($value)
    {
        return array($this->name => $value);
    }

    /**
     * @param array $value
     *
     * @return array
     */
    public function reverseTransform($value)
    {
        return is_array($value) && array_key_exists($this->name, $value) ? $value[$this->name] : array();
    }
}
