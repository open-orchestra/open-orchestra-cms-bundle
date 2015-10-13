<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use OpenOrchestra\ApiBundle\Transformer\ContentAttributeTransformer;
use Phake;

/**
 * Class ContentAttributeTransformerTest
 */
class ContentAttributeTransformerTest extends \PHPUnit_Framework_TestCase
{
    protected $contentAttribute;
    protected $transformer;

    /**
     * set Up
     */
    public function setUp()
    {
        $this->contentAttribute = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentAttributeInterface');
        $this->transformer = new ContentAttributeTransformer();
    }

    /**
     * test transform
     */
    public function testTransform()
    {
        $facade = $this->transformer->transform($this->contentAttribute);

        Phake::verify($this->contentAttribute, Phake::times(1))->getName();
        Phake::verify($this->contentAttribute, Phake::times(1))->getValue();
        Phake::verify($this->contentAttribute, Phake::times(1))->getStringValue();
    }

    /**
     * test getName
     */
    public function testGetName()
    {
        $this->assertSame('content_attribute', $this->transformer->getName());
    }
}