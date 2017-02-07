<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use OpenOrchestra\ModelInterface\Model\FieldOptionInterface;

/**
 * Class FieldOptionTransformer
 */
class FieldOptionTransformer implements DataTransformerInterface
{
    protected $fieldOptionClass;

    /**
     * @param string $fieldOptionClass
     */
    public function __construct($fieldOptionClass)
    {
        $this->fieldOptionClass = $fieldOptionClass;
    }

    /**
     * @param array $fieldOptions
     *
     * @return array
     */
    public function transform($fieldOptions)
    {
        $result = array();
        if (is_array($fieldOptions)) {
            foreach ($fieldOptions as $fieldOption) {
                if($fieldOption instanceof FieldOptionInterface) {
                    $result[$fieldOption->getKey()] = $fieldOption->getValue();
                }
            }
        }

        return $result;
    }

    /**
     * @param array $fieldOptions
     *
     * @return array
     */
    public function reverseTransform($fieldOptions)
    {
        $result = array();
        $fieldOptionClass = $this->fieldOptionClass;

        foreach ($fieldOptions as $key => $value) {
            $fieldOption = new $fieldOptionClass();
            $fieldOption->setKey($key);
            $fieldOption->setValue($value);
            $result[] = $fieldOption;
        }

        return $result;
    }
}
