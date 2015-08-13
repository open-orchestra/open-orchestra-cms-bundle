<?php

namespace OpenOrchestra\ApiBundle\Tests\Mapping;

use OpenOrchestra\ApiBundle\Mapping\MappingContentAttribute;
use Phake;

class MappingContentAttributeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MappingContentAttribute
     */
    protected $mapping;
    protected $contentTypeReposioty;
    protected $contentType;
    protected $field;

    /**
     * Set up test
     */
    public function setUp()
    {
        $this->contentTypeReposioty = Phake::Mock('OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface');
        $this->contentType = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        $this->field = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');

        Phake::when($this->contentTypeReposioty)->findOneByContentTypeIdInLastVersion(Phake::anyParameters())->thenReturn($this->contentType);
        Phake::when($this->contentType)->getFields()->thenReturn(array($this->field));

        $this->mapping = new MappingContentAttribute($this->contentTypeReposioty);
    }

    /**
     * test getMapping
     */
    public function testGetMapping()
    {
        $fieldId = 'fakeId';
        Phake::when($this->field)->getFieldId()->thenReturn($fieldId);
        $mapping = $this->mapping->getMapping('fakeContentTypeId');

        $key = 'attributes.'.$fieldId.'.string_value';
        $field = 'attributes.'.$fieldId.'.stringValue';

        $this->assertCount(1, $mapping);
        $this->assertInstanceOf('OpenOrchestra\Mapping\Annotations\Search', $mapping[$key]);
        $this->assertSame($field, $mapping[$key]->getField());
        $this->assertSame($key, $mapping[$key]->getKey());
    }
}
