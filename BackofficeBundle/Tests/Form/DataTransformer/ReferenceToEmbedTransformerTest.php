<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\DataTransformer;

use OpenOrchestra\BackofficeBundle\Form\DataTransformer\ReferenceToEmbedTransformer;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class ReferenceToEmbedTransformerTest
 */
class ReferenceToEmbedTransformerTest extends AbstractBaseTestCase
{
    protected $transformer;
    protected $documentClass = 'OpenOrchestra\BackofficeBundle\Tests\Form\DataTransformer\FakeDocument';
    protected $formTypeName = 'fakeFormTypeName';
    protected $id = 'fakeId';
    protected $data;
    protected $document;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->data = array('id' => $this->id);
        $this->transformData = array($this->formTypeName => $this->id);

        $entityDbMapper = Phake::mock('OpenOrchestra\ModelInterface\Manager\EntityDbMapperInterface');
        $objectManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');

        $this->document = new FakeDocument($this->id);

        Phake::when($entityDbMapper)->fromDbToEntity($this->data)->thenReturn($this->document);
        Phake::when($entityDbMapper)->fromEntityToDb($this->document)->thenReturn($this->data);
        Phake::when($objectManager)->find($this->documentClass, $this->id)->thenReturn($this->document);

        $this->transformer = new ReferenceToEmbedTransformer($entityDbMapper, $objectManager, $this->documentClass);
        $this->transformer->setFormTypeName($this->formTypeName);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\DataTransformerInterface', $this->transformer);
    }

    /**
     * test transform
     */
    public function testTransform()
    {
        $this->assertSame($this->transformData, $this->transformer->transform($this->data));
    }

    /**
     * test reverseTransform
     */
    public function testReverseTransform()
    {
        $this->assertSame($this->data, $this->transformer->reverseTransform($this->transformData));
    }
}

class FakeDocument
{
    protected $id;

    public function __construct($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }
}
