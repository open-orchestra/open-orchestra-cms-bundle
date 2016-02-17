<?php

namespace OpenOrchestra\Backoffice\Tests\Form\BBcodeToHtmlTrandformerTest;

use OpenOrchestra\Backoffice\Form\DataTransformer\HtmlElementTransformer;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class HtmlElementTransformerTest
 */
class HtmlElementTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HtmlElementTransformer
     */
    protected $transformer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->transformer = new HtmlElementTransformer();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(DataTransformerInterface::CLASS, $this->transformer);
    }

    /**
     * Test transform
     *
     * @param mixed $data
     *
     * @dataProvider provideDataToTransform
     */
    public function testTransform($data)
    {
        $this->assertSame($data, $this->transformer->transform($data));
    }

    /**
     * @return array
     */
    public function provideDataToTransform()
    {
        return array(
            array('foo'),
            array('bar'),
            array(1),
        );
    }

    /**
     * @param string $expected
     * @param string $data
     *
     * @dataProvider provideReverseTransformData
     */
    public function testReverseTransform($expected, $data)
    {
        $this->assertSame($expected, $this->transformer->reverseTransform($data));
    }

    /**
     * @return array
     */
    public function provideReverseTransformData()
    {
        return array(
            array('foo', 'foo'),
            array('-foo-', '"foo"'),
            array('[-----]', '["\'<> ]')
        );
    }
}
