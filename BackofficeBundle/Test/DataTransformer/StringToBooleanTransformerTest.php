<?php

namespace PHPOrchestra\BackofficeBundle\Test\DataTransformer;

use PHPOrchestra\BackofficeBundle\Form\DataTransformer\StringToBooleanTransformer;

class StringToBooleanTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StringToBooleanTransformer
     */
    protected $transformer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->transformer = new StringToBooleanTransformer();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\DataTransformerInterface', $this->transformer);
    }

    /**
     * @param mixed $value
     * @param bool  $expected
     *
     * @dataProvider generateTransformProvider
     */
    public function testTransform($value, $expected)
    {
        $this->assertSame($expected, $this->transformer->transform($value));
    }

    /**
     * @return array
     */
    public function generateTransformProvider()
    {
        return array(
            array(true, true),
            array(false, false),
            array(null, false),
            array('', false),
            array('0', false),
            array(0, false),
            array('1', true),
            array(1, true),
        );
    }

    /**
     * @param mixed  $value
     * @param string $exception
     *
     * @dataProvider generateTransformExceptionProvider
     */
    public function testGetException($value, $exception)
    {
        $this->setExpectedException($exception);
        $this->transformer->transform($value);
    }

    /**
     * @return array
     */
    public function generateTransformExceptionProvider()
    {
        return array(
            array(array('test' => 'exception'), 'Symfony\Component\Form\Exception\TransformationFailedException'),
        );
    }

    /**
     * @param mixed  $value
     * @param string $expected
     *
     * @dataProvider generateReverseTransformProvider
     */
    public function testReverseTransform($value, $expected)
    {
        $this->assertSame($expected, $this->transformer->reverseTransform($value));
    }

    /**
     * @return array
     */
    public function generateReverseTransformProvider()
    {
        return array(
            array(true, '1'),
            array(false, '0'),
            array(1, '1'),
            array(0, '0')
        );
    }

    /**
     * @param mixed  $value
     * @param string $exception
     *
     * @dataProvider generateReverseTransformExceptionProvider
     */
    public function testReverseTransformException($value, $exception)
    {
        $this->markTestSkipped();
        $this->setExpectedException($exception);
        $this->transformer->reverseTransform($value);
    }

    /**
     * @return array
     */
    public function generateReverseTransformExceptionProvider()
    {
        return array(
            array('test', 'Symfony\Component\Form\Exception\TransformationFailedException'),
            array('', 'Symfony\Component\Form\Exception\TransformationFailedException'),
            array(null, 'Symfony\Component\Form\Exception\TransformationFailedException'),
            array(array('test' => 'exception'), 'Symfony\Component\Form\Exception\TransformationFailedException'),
        );
    }
}
