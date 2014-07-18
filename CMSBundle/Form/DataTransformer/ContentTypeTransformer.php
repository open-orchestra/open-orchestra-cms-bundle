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
    protected $customTypes = array();

    /**
     * Constructor
     * 
     * @param array $customTypes list of availables custom Types
     */
    public function __construct($customTypes = array())
    {
        if (is_array($customTypes)) {
            $this->customTypes = $customTypes;
        }
    }

    /**
     * Transforms a ContentType entity to inject customfields as standard fields
     *
     * @param object ContentType
     * @return object
     */
    public function transform($contentType) // entity => formfield
    {
        $contentType->new_field = '';
        
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
        if ($contentType->new_field != ''
            && array_key_exists($contentType->new_field, $this->customTypes)
            && array_key_exists('type', $this->customTypes[$contentType->new_field])
        ) {
            $fieldStructure = $this->customTypes[$contentType->new_field];
            $fieldOptions = array();
            
            if (is_array($fieldStructure) && array_key_exists('options', $fieldStructure)) {
                foreach ($fieldStructure['options'] as $optionType => $optionParams) {
                    $fieldOptions[$optionType] = $optionParams['default_value'];
                }
            }
            
            $fields = json_decode($contentType->getFields());
            $fields[] = (object) array(
                'fieldId' => '',
                'label' => '',
                'defaultValue' => '',
                'searchable' => false,
                'type' => $contentType->new_field,
                'symfonyType' => $fieldStructure['type'],
                'options' => (object) $fieldOptions
            );
            
            $contentType->setFields(json_encode($fields));
        }
        
        return $contentType;
    }
}
