<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\DataTransformer;

use OpenOrchestra\BackofficeBundle\Form\DataTransformer\BlockToArrayTransformer;
use Phake;

/**
 * Test BlockToArrayTransformerTest
 */
class BlockToArrayTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BlockToArrayTransformer
     */
    protected $transformer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->transformer = new BlockToArrayTransformer();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\DataTransformerInterface', $this->transformer);
    }

    /**
     * Test transform with empty data
     *
     * @param mixed $data
     *
     * @dataProvider provideEmptyData
     */
    public function testTransformEmpty($data)
    {
        $this->assertSame($data, $this->transformer->transform($data));
    }

    /**
     * @return array
     */
    public function provideEmptyData()
    {
        return array(
            array(array()),
            array(null),
            array(''),
        );
    }

    /**
     * Test transform block
     */
    public function testTransform()
    {
        $element = 'element';

        $data = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($data)->getLabel()->thenReturn($element);
        Phake::when($data)->getClass()->thenReturn($element);
        Phake::when($data)->getId()->thenReturn($element);
        Phake::when($data)->getMaxAge()->thenReturn($element);
        Phake::when($data)->getAttributes()->thenReturn(array('foo' => $element));

        $this->assertSame(array(
            'label' => $element,
            'class' => $element,
            'id' => $element,
            'maxAge' => $element,
            'foo' => $element
        ), $this->transformer->transform($data));
    }
}
