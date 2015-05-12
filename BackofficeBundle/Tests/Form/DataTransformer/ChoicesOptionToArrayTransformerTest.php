<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\DataTransformer;

use OpenOrchestra\BackofficeBundle\Form\DataTransformer\ChoicesOptionToArrayTransformer;
use OpenOrchestra\BackofficeBundle\Form\DataTransformer\SuppressSpecialCharacterTransformer;
use Phake;

/**
 * Class ChoicesOptionToArrayTransformerTest
 */
class ChoicesOptionToArrayTransformerTest extends \PHPUnit_Framework_TestCase
{
    protected $suppressSpecialCharacter;
    protected $transformer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->suppressSpecialCharacter = new SuppressSpecialCharacterTransformer();
        $this->transformer = new ChoicesOptionToArrayTransformer($this->suppressSpecialCharacter);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\DataTransformerInterface', $this->transformer);
    }

    /**
     * Test with null data
     */
    public function testTransformWithNullData()
    {
        $this->assertSame('', $this->transformer->transform(null));
    }

    /**
     * @param $data
     * @param $transformData
     *
     * @dataProvider providerDifferentArray
     */
    public function testTransformWithArrayData($data, $transformData)
    {
        $this->assertSame($transformData, $this->transformer->transform($data));
    }

    /**
     * @return array
     */
    public function providerDifferentArray()
    {
        return array(
            array(array('choice1','choice2'),'choice1,choice2'),
            array(array('choice1'),'choice1'),
            array(array(),''),
        );
    }

    /**
     * @param $data
     * Test with null data
     *
     * @dataProvider providerDifferentEmptyData
     */
    public function testReverseTransformWithNullAndEmptyData($data)
    {
        $this->assertSame(array(), $this->transformer->reverseTransform($data));
    }

    /**
     * @return array
     */
    public function providerDifferentEmptyData()
    {
        return array(
            array(null),
            array(''),
            array('  '),
        );
    }

    /**
     * @param $data
     * @param $transformData
     *
     * @dataProvider providerDifferentStringData
     */
    public function testReverseTransformWithStringData($data, $transformData){
        $this->assertSame($transformData, $this->transformer->reverseTransform($data));
    }

    /**
     * @return array
     */
    public function providerDifferentStringData()
    {
        return array(
            array(
                'choice1,choice2',
                array('choice1' => 'choice1', 'choice2' => 'choice2'),
            ),
            array(
                'choice,""',
                array('choice' => 'choice'),
            ),
            array('choîce@=!',
                array('choice' => 'choi ce'),
            ),
        );
    }
}
