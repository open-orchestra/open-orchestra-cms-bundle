<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use OpenOrchestra\ApiBundle\Transformer\ContentAttributeTransformer;
use Phake;

/**
 * Class ContentAttributeTransformerTest
 */
class ContentAttributeTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentAttributeTransformer
     */
    protected $transformer;
    protected $facadeClass = 'OpenOrchestra\ApiBundle\Facade\ContentAttributeFacade';

    /**
     * set Up
     */
    public function setUp()
    {
        $this->transformer = new ContentAttributeTransformer($this->facadeClass);
    }

    /**
     * @param string $name
     * @param string $value
     * @param string $stringValue
     *
     * @dataProvider provideContentAttributeData
     */
    public function testTransform($name, $value, $stringValue)
    {
        $contentAttribute = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentAttributeInterface');
        Phake::when($contentAttribute)->getName()->thenReturn($name);
        Phake::when($contentAttribute)->getValue()->thenReturn($value);
        Phake::when($contentAttribute)->getStringValue()->thenReturn($stringValue);

        $facade = $this->transformer->transform($contentAttribute);

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\ContentAttributeFacade', $facade);
        $this->assertSame($name, $facade->name);
        $this->assertSame($value, $facade->value);
        $this->assertSame($stringValue, $facade->stringValue);
    }

    /**
     * @return array
     */
    public function provideContentAttributeData()
    {
        return array(
            array('foo', 'bar', 'baz'),
            array('bar', 'baz', 'foo'),
        );
    }

    /**
     * Test Exception transform with wrong object a parameters
     */
    public function testExceptionTransform()
    {
        $this->setExpectedException('OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException');
        $this->transformer->transform(Phake::mock('stdClass'));
    }

    /**
     * test getName
     */
    public function testGetName()
    {
        $this->assertSame('content_attribute', $this->transformer->getName());
    }
}
