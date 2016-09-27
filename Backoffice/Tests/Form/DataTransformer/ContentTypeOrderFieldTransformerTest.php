<?php

namespace OpenOrchestra\Backoffice\Tests\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\Backoffice\Form\DataTransformer\ContentTypeOrderFieldTransformer;
use Symfony\Component\Form\DataTransformerInterface;
use Phake;

/**
 * Class ContentTypeOrderFieldTransformerTest
 */
class ContentTypeOrderFieldTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentTypeOrderFieldTransformer
     */
    protected $transformer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->transformer = new ContentTypeOrderFieldTransformer();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(DataTransformerInterface::CLASS, $this->transformer);
    }

    /**
     * Test transform
     *
     * @param mixed $data
     *
     * @dataProvider provideDataToTransform
     */
    public function testTransform($data)
    {
        $this->assertSame($data, $this->transformer->transform($data));
    }

    /**
     * @return array
     */
    public function provideDataToTransform()
    {
        return array(
            array(new ArrayCollection(array("test"))),
            array('bar'),
            array(1),
        );
    }

    /**
     * @param ArrayCollection $data
     * @param ArrayCollection $expected
     *
     * @dataProvider provideFields
     */
    public function testReverseTransform($data, ArrayCollection $expected)
    {
        $fields = $this->transformer->reverseTransform($data);
        foreach ($fields as $key => $field) {
            $this->assertSame($field, $expected->get($key));
        }
    }

    /**
     * @return array
     */
    public function provideFields()
    {
        $field1 = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($field1)->getPosition()->thenReturn(0);

        $field2 = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($field2)->getPosition()->thenReturn(0);

        $field3 = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($field3)->getPosition()->thenReturn(2);

        $field4 = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($field4)->getPosition()->thenReturn(5);

        return array(
            array(new ArrayCollection(array($field1, $field2)), new ArrayCollection(array($field1, $field2))),
            array(new ArrayCollection(array($field2, $field1)), new ArrayCollection(array($field2, $field1))),
            array(new ArrayCollection(array($field2, $field3,  $field1)), new ArrayCollection(array($field1, $field2, $field3))),
            array(new ArrayCollection(array($field4, $field2, $field3,  $field1)), new ArrayCollection(array($field2, $field1, $field3, $field4))),
        );
    }
}
