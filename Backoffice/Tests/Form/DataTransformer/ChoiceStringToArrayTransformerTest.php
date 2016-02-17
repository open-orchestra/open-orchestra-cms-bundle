<?php

namespace OpenOrchestra\Backoffice\Tests\Form\DataTransformer;

use OpenOrchestra\Backoffice\Form\DataTransformer\ChoiceStringToArrayTransformer;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;

/**
 * Class ChoiceStringToArrayTransformerTest
 */
class ChoiceStringToArrayTransformerTest extends AbstractBaseTestCase
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
