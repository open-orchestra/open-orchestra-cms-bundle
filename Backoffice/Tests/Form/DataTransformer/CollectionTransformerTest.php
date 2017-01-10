<?php

namespace OpenOrchestra\Backoffice\Tests\Form\DataTransformer;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\Backoffice\Form\DataTransformer\CollectionTransformer;

/**
 * Class CollectionTransformerTest
 */
class CollectionTransformerTest extends AbstractBaseTestCase
{
    /**
     * @var CollectionTransformer
     */
    protected $transformer;
    protected $name = 'fakeName';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->transformer = new CollectionTransformer($this->name);
    }

    /**
     * Test Transform
     */
    public function testTransform()
    {
        $result = $this->transformer->transform(array());

        $this->assertArrayHasKey($this->name, $result);
    }

    /**
     * Test reverseTransform
     *
     * @param mixed $data
     * @param array $expectedReturn
     *
     * @dataProvider provideValue
     */
    public function testReverseTransform($data, array $expectedReturn)
    {
        $result = $this->transformer->reverseTransform($data);

        $this->assertSame($expectedReturn, $result);
    }

    /**
     * @return array
     */
    public function provideValue()
    {
        return array(
            array(null, array()),
            array(array(1, 2, 3), array()),
            array(array('fakeName' => array('result')), array('result')),
        );
    }
}
