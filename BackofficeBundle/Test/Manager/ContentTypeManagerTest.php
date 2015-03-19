<?php

namespace OpenOrchestra\BackofficeBundle\Test\Manager;

use OpenOrchestra\BackofficeBundle\Manager\ContentTypeManager;
use Phake;

/**
 * Class ContentTypeManagerTest
 */
class ContentTypeManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContenttypeManager
     */
    protected $manager;
    protected $contentType;

    /**
     * Set Up the test
     */
    public function setUp()
    {
        $this->contentType = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');

        $this->manager = new ContentTypeManager();
    }

    /**
     * Test delete
     */
    public function testDelete()
    {
        $this->manager->delete(array($this->contentType, $this->contentType, $this->contentType));

        Phake::verify($this->contentType, Phake::times(3))->setDeleted(true);
    }
}
