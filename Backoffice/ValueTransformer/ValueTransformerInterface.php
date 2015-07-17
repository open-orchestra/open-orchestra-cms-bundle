<?php

namespace OpenOrchestra\Backoffice\ValueTransformer;

/**
 * ValueTransformerInterface
 */
interface ValueTransformerInterface
{
    /**
     * @param mixed $data
     *
     * @return string
     */
    public function transform($data);

    /**
     * @param string $fieldType
     * @param mixed  $value
     *
     * @return bool
     */
    public function support($fieldType, $value);

    /**
     * @return string
     */
    public function getName();
}
