<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use OpenOrchestra\ApiBundle\Transformer\ContentAttributeTransformer;
use Phake;

/**
 * Class ContentAttributeTransformerTest
 */
class ContentAttributeTransformerTest extends \PHPUnit_Framework_TestCase
{
    protected $contentAttributeString;
    protected $contentAttribute;
    protected $transformer;

    /**
     * set Up
     */
    public function setUp()
    {
        $this->contentAttribute = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentAttributeInterface');
        $this->transformer = new ContentAttributeTransformer();
        $this->contentAttributeString = 'ContentAttributeString';
        Phake::when($this->contentAttribute)->getName()->thenReturn($this->contentAttributeString);
        Phake::when($this->contentAttribute)->getValue()->thenReturn($this->contentAttributeString);
        Phake::when($this->contentAttribute)->getStringValue()->thenReturn($this->contentAttributeString);
    }

    /**
     * test transform
     */
    public function testTransform()
    {
        $facade = $this->transformer->transform($this->contentAttribute);

        $this->assertSame($this->contentAttributeString, $facade->name);
        $this->assertSame($this->contentAttributeString, $facade->value);
        $this->assertSame($this->contentAttributeString, $facade->stringValue);
    }

    /**
     * test getName
     */
    public function testGetName()
    {
        $this->assertSame('content_attribute', $this->transformer->getName());
    }
}
