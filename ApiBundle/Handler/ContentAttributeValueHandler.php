<?php

namespace OpenOrchestra\ApiBundle\Handler;

use JMS\Serializer\GenericDeserializationVisitor;
use JMS\Serializer\GenericSerializationVisitor;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Context;
use OpenOrchestra\ApiBundle\Facade\ContentAttributeFacade;

/**
 * Class ContentAttributeValueHandler
 */
class ContentAttributeValueHandler implements SubscribingHandlerInterface
{
    protected $fieldTypes;

    /**
     * @param array $fieldTypes
     */
    public function __construct(array $fieldTypes)
    {
        $this->fieldTypes = $fieldTypes;
    }

    /**
     * @return array
     */
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => 'ContentAttributeValue',
                'method' => 'deserializeFieldType',
            ),
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'ContentAttributeValue',
                'method' => 'serializeFieldType',
            ),
            array(
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => 'xml',
                'type' => 'ContentAttributeValue',
                'method' => 'deserializeFieldType',
            ),
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'xml',
                'type' => 'ContentAttributeValue',
                'method' => 'serializeFieldType',
            ),
        );
    }

    /**
     * @param GenericDeserializationVisitor $visitor
     * @param string                        $contentAttributeValue
     * @param array                         $type
     * @param Context                       $context
     *
     * @return null|string
     */
    public function deserializeFieldType(GenericDeserializationVisitor $visitor, $contentAttributeValue, array $type, Context $context)
    {
        $facade = $visitor->getCurrentObject();
        if ($facade instanceof ContentAttributeFacade && null !== $facade->type) {
            if (isset($this->fieldTypes[$facade->type]) &&
                isset($this->fieldTypes[$facade->type]['deserialize_type'])
            ) {
                if ($this->fieldTypes[$facade->type]['deserialize_type'] == 'array' && is_scalar($contentAttributeValue)) {
                    $contentAttributeValue = array($contentAttributeValue);
                }

                return $visitor->getNavigator()->accept($contentAttributeValue, array('name' => $this->fieldTypes[$facade->type]['deserialize_type'], 'params' => array()), $context);
            }
        }

        return null;
    }

    /**
     * @param GenericSerializationVisitor $visitor
     * @param string                      $contentAttributeValue
     * @param array                       $type
     * @param Context                     $context
     *
     * @return null|string
     */
    public function serializeFieldType(GenericSerializationVisitor $visitor, $contentAttributeValue, array $type, Context $context)
    {
        return $visitor->getNavigator()->accept($contentAttributeValue, null, $context);
    }
}
