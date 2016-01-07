<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Manager;

use OpenOrchestra\BackofficeBundle\Manager\ContentTypeManager;
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
            'status_label'   => false,
            'version'        => false,
            'language'       => false,
            'linked_to_site' => true,
            'created_at'     => true,
            'created_by'     => true,
            'updated_at'     => false,
            'updated_by'     => false
        ), $contentType->getDefaultListable());
    }
}
