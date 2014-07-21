<?php
/**
 * This file is part of the PHPOrchestra\CMSBundle.
 *
 * @author Noël Gilain <noel.gilain@businessdecision.com>
 */

namespace PHPOrchestra\CMSBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class ContentTypeTransformer implements DataTransformerInterface
{
    protected $serializer = null;
    protected $customTypes = array();

    /**
     * Constructor
     * 
     * @param array $customTypes list of availables custom Types
     */
    public function __construct($serializer, array $customTypes = array())
    {
        $this->serializer = $serializer;
        $this->customTypes = $customTypes;
    }

    /**
     * Transforms a ContentType entity to inject customfields as standard fields
     *
     * @param object ContentType
     * @return object
     */
    public function transform($contentType) // entity => formfield
    {
        $contentType->newField = '';
        
        return $contentType;
    }

    /**
     * Transforms an object in valid ContentType entity.
     *
     * @param  object $contentType
     * @return object
     */
    public function reverseTransform($contentType) // formfield => entity
    {
        if ($contentType->newField != ''
            && array_key_exists($contentType->newField, $this->customTypes)
            && array_key_exists('type', $this->customTypes[$contentType->newField])
        ) {
            $fieldStructure = $this->customTypes[$contentType->newField];
            $fieldOptions = array();
            
            if (is_array($fieldStructure) && array_key_exists('options', $fieldStructure)) {
                foreach ($fieldStructure['options'] as $optionType => $optionParams) {
                    $fieldOptions[$optionType] = $optionParams['default_value'];
                }
            }
            
            $fields = $this->serializer->deserialize($contentType->getFields(), 'array', 'json');
            
            $fields[] = array(
                'fieldId' => '',
                'label' => '',
                'defaultValue' => '',
                'searchable' => false,
                'type' => $contentType->newField,
                'symfonyType' => $fieldStructure['type'],
                'options' => $fieldOptions
            );
            
            $contentType->setFields($this->serializer->serialize($fields, 'json'));
        }
        
        return $contentType;
    }
}
