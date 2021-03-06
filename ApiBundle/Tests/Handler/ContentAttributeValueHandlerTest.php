<?php

namespace OpenOrchestra\ApiBundle\Tests\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use OpenOrchestra\ApiBundle\Handler\ContentAttributeValueHandler;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class ContentAttributeValueHandlerTest
 */
class ContentAttributeValueHandlerTest extends AbstractBaseTestCase
{
    /** @var ContentAttributeValueHandler */
    protected $handler;

    /**
     * Set up test
     */
    public function setUp()
    {
        $fieldTypes = array(
            'text' => array(
                'deserialize_type' => 'string'
            ),
            'boolean' => array(
                'deserialize_type' => 'boolean'
            ),
        );
        $this->handler = new ContentAttributeValueHandler($fieldTypes);
    }

    /**
     * Test SubscribingMethods
     */
    public function testSubscribingMethods()
    {
        $this->assertSame(
            array(
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
                )
            ),
            ContentAttributeValueHandler::getSubscribingMethods()
        );
    }

    /**
     * @param $type
     * @param $contentAttributeValue
     * @param $expected
     *
     * @dataProvider provideTypeAndContentAttributeValue
     */
    public function testDeserializeFieldTypeToJson($type, $contentAttributeValue, $expected)
    {
        $visitor = Phake::mock('JMS\Serializer\GenericDeserializationVisitor');
        $context = Phake::mock('JMS\Serializer\Context');
        $contentAttributeFacade = Phake::mock('OpenOrchestra\ApiBundle\Facade\ContentAttributeFacade');
        $contentAttributeFacade->type = $type;

        Phake::when($visitor)->getCurrentObject()->thenReturn($contentAttributeFacade);
        Phake::when($visitor)->getNavigator()->thenReturn(new FakeGraphNavigator());

        $this->assertEquals($expected, $this->handler->deserializeFieldType($visitor, $contentAttributeValue, array(), $context));
    }

    /**
     * @return array
     */
    public function provideTypeAndContentAttributeValue()
    {
        return array(
            array(null, 'fakeAttribute', null),
            array(null, null, null),
            array('text', 'fakeAttribute', FakeGraphNavigator::FAKE_RETURN_ACCEPT),
            array('fakeType', 'fakeAttribute', null),
            array('boolean', '1', FakeGraphNavigator::FAKE_RETURN_ACCEPT),
        );
    }

    /**
     * Test serialize field type to json
     */
    public function testSerializeFieldTypeToJson()
    {
        $contentAttributeValue = 'fakeValue';
        $visitor = Phake::mock('JMS\Serializer\GenericSerializationVisitor');
        $context = Phake::mock('JMS\Serializer\Context');

        Phake::when($visitor)->getNavigator()->thenReturn(new FakeGraphNavigator());

        $this->handler->serializeFieldType($visitor, $contentAttributeValue, array(), $context);

        $this->assertEquals(FakeGraphNavigator::FAKE_RETURN_ACCEPT, $this->handler->SerializeFieldType($visitor, $contentAttributeValue, array(), $context));
    }
}


class FakeGraphNavigator
{
    const FAKE_RETURN_ACCEPT = 'fakeReturnAccept';

    /**
     * @param string     $data
     * @param array|null $type
     * @param Context    $context
     *
     * @return string
     */
    public function accept($data, array $type = null, Context $context)
    {
        return self::FAKE_RETURN_ACCEPT;
    }
}
