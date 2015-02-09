<?php

namespace PHPOrchestra\BackofficeBundle\Test\DataTransformer;

use PHPOrchestra\BackofficeBundle\Form\DataTransformer\OrchestraMediaTransformer;
use PHPOrchestra\Media\Model\MediaInterface;

/**
 * Class OrchestraMediaTransformerTest
 */
class OrchestraMediaTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OrchestraMediaTransformer
     */
    protected $transformer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->transformer = new OrchestraMediaTransformer();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\DataTransformerInterface', $this->transformer);
    }

    /**
     * @param string $value
     * @param string $expected
     *
     * @dataProvider provideTransformData
     */
    public function testTransform($value, $expected)
    {
        $this->assertSame($expected, $this->transformer->transform($value));
    }

    /**
     * @return array
     */
    public function provideTransformData()
    {
        return array(
            array('', ''),
            array(MediaInterface::MEDIA_PREFIX  . 'id', 'id'),
            array('id', 'id'),
        );
    }

    /**
     * @param string $value
     * @param string $expected
     *
     * @dataProvider provideReverseTransformData
     */
    public function testReverseTransform($value, $expected)
    {
        $this->assertSame($expected, $this->transformer->reverseTransform($value));
    }

    /**
     * @return array
     */
    public function provideReverseTransformData()
    {
        return array(
            array('', ''),
            array('id', MediaInterface::MEDIA_PREFIX . 'id'),
            array(MediaInterface::MEDIA_PREFIX . 'id', MediaInterface::MEDIA_PREFIX . 'id'),
        );
    }
}
