<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\DataTransformer;

use OpenOrchestra\BackofficeBundle\Form\DataTransformer\ChoiceArrayToStringTransformer;

/**
 * Class ChoiceArrayToStringTransformerTest
 */
class ChoiceArrayToStringTransformerTest extends \PHPUnit_Framework_TestCase
{
    protected $transformer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->transformer = new ChoiceArrayToStringTransformer();
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
            array(null,''),
            array(array('choice1'),'choice1'),
            array('choice1','choice1'),
            array(1,'1'),
            array(array(),''),
        );
    }
}
