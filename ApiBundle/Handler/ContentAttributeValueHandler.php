<?php

namespace OpenOrchestra\ApiBundle\Handler;

use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\JsonDeserializationVisitor;
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
                'method' => 'deserializeFieldTypeToJson',
            ),
        );
    }

    /**
     * @param JsonDeserializationVisitor $visitor
     * @param string                     $contentAttributeValue
     * @param array                      $type
     * @param Context                    $context
     *
     * @return mixed|string
     */
    public function deserializeFieldTypeToJson(JsonDeserializationVisitor $visitor, $contentAttributeValue, array $type, Context $context)
    {
        $facade = $visitor->getCurrentObject();
        if ($facade instanceof ContentAttributeFacade && null !== $facade->type) {
            if (isset($this->fieldTypes[$facade->type]) &&
                isset($this->fieldTypes[$facade->type]['deserialize_type'])
            ) {
                return $visitor->getNavigator()->accept($contentAttributeValue, array('name' => $this->fieldTypes[$facade->type]['deserialize_type']), $context);
            }
        }

        return null;
    }
}
