<?php

namespace OpenOrchestra\Backoffice\Tests\Manager;

use OpenOrchestra\Backoffice\Manager\ContentTypeManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class ContentTypeManagerTest
 */
class ContentTypeManagerTest extends AbstractBaseTestCase
{
    /**
     * @var ContenttypeManager
     */
    protected $manager;

    protected $contentType;
    protected $contentTypeClass = 'OpenOrchestra\ModelBundle\Document\ContentType';

    /**
     * Set Up the test
     */
    public function setUp()
    {
        $this->contentType = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');

        $this->manager = new ContentTypeManager($this->contentTypeClass);
    }

    /**
     * Test delete
     */
    public function testDelete()
    {
        $this->manager->delete(array($this->contentType, $this->contentType, $this->contentType));

        Phake::verify($this->contentType, Phake::times(3))->setDeleted(true);
    }

    /**
     * Test initialization
     */
    public function testInitializeNewContentType()
    {
        $contentType = $this->manager->initializeNewContentType();

        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\ContentTypeInterface', $contentType);
        $this->assertSame(array(
            'name'           => true,
            'linked_to_site' => false,
            'created_at'     => false,
            'created_by'     => true,
            'updated_at'     => true,
            'updated_by'     => false,
            'status'         => true,
        ), $contentType->getDefaultListable());
    }
}
