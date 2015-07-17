<?php

namespace OpenOrchestra\BackofficeBundle\Tests\ValueTransformer\Strategies;

/**
 * Class AbstractTransformerTest
 */
abstract class AbstractTransformerTest extends \PHPUnit_Framework_TestCase
{
    public $transformer;

    /**
     * @param $value
     * @param $expected
     *
     * @dataProvider provideTransform
     */
    public function testTransform($value, $expected)
    {
        $output = $this->transformer->transform($value);
        $this->assertEquals($output, $expected);
    }

    /**
     * @return array
     */
    abstract public function provideTransform();

    /**
     * @param string $fieldType
     * @param string $value
     * @param bool   $expected
     *
     * @dataProvider provideSupport
     */
    public function testSupport($fieldType, $value, $expected)
    {
        $output = $this->transformer->support($fieldType, $value);
        $this->assertEquals($output, $expected);
    }

    /**
     * @return array
     */
    abstract public function provideSupport();

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertEquals($this->transformer->getName(), $this->getTransformerName());
    }

    /**
     * @return string
     */
    abstract protected function getTransformerName();
}
