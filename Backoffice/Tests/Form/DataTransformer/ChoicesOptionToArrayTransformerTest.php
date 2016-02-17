<?php

namespace OpenOrchestra\Backoffice\Tests\Form\DataTransformer;

use OpenOrchestra\Backoffice\Form\DataTransformer\ChoicesOptionToArrayTransformer;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class ChoicesOptionToArrayTransformerTest
 */
class ChoicesOptionToArrayTransformerTest extends AbstractBaseTestCase
{
    protected $suppressSpecialCharacterHelper;
    protected $transformer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->suppressSpecialCharacterHelper = Phake::mock('OpenOrchestra\ModelInterface\Helper\SuppressSpecialCharacterHelperInterface');
        $this->transformer = new ChoicesOptionToArrayTransformer($this->suppressSpecialCharacterHelper);
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
        Phake::when($this->suppressSpecialCharacterHelper)->transform('', array('_','.'))->thenReturn('');
        Phake::when($this->suppressSpecialCharacterHelper)->transform('choice', array('_','.'))->thenReturn('choice');
        Phake::when($this->suppressSpecialCharacterHelper)->transform('choice2', array('_','.'))->thenReturn('choice2');
        Phake::when($this->suppressSpecialCharacterHelper)->transform('choîce@=!', array('_','.'))->thenReturn('choice');
        Phake::when($this->suppressSpecialCharacterHelper)->transform('translate.choice_1', array('_','.'))->thenReturn('translate.choice_1');
        Phake::when($this->suppressSpecialCharacterHelper)->transform('translate.choîce@=!_2', array('_','.'))->thenReturn('translate.choice_2');

        $this->assertSame($transformData, $this->transformer->reverseTransform($data));
    }

    /**
     * @return array
     */
    public function providerDifferentStringData()
    {
        return array(
            array(
                'choice,choice2',
                array('choice' => 'choice', 'choice2' => 'choice2'),
            ),
            array(
                'choice,""',
                array('choice' => 'choice'),
            ),
            array('choîce@=!',
                array('choice' => 'choice'),
            ),
            array('translate.choice_1,translate.choîce@=!_2',
                array('translate.choice_1' => 'translate.choice_1', 'translate.choice_2' => 'translate.choice_2'),
            ),
        );
    }
}
