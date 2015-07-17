<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\DataTransformer;

use OpenOrchestra\BackofficeBundle\Form\DataTransformer\ChoiceStringToArrayTransformer;

/**
 * Class ChoiceStringToArrayTransformerTest
 */
class ChoiceStringToArrayTransformerTest extends \PHPUnit_Framework_TestCase
{
    protected $transformer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->transformer = new ChoiceStringToArrayTransformer();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\DataTransformerInterface', $this->transformer);
    }

    /**
     * @param $data
     * @param $transformData
     *
     * @dataProvider providerData
     */
    public function testTransform($data, $transformData)
    {
        $this->assertSame($transformData, $this->transformer->transform($data));
    }

    /**
     * @param $data
     *
     * @dataProvider providerData
     */
    public function testReverseTransform($data)
    {
        $this->assertSame($data, $this->transformer->reverseTransform($data));
    }

    /**
     * @return array
     */
    public function providerData()
    {
        return array(
            array('', array()),
            array(null, array()),
            array('choice1', array('choice1')),
            array(array('choice1'), array('choice1')),
        );
    }
}
