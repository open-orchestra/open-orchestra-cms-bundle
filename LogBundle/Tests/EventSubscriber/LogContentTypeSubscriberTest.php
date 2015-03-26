<?php

namespace OpenOrchestra\LogBundle\Tests\EventSubscriber;

use Phake;
use OpenOrchestra\LogBundle\EventSubscriber\LogContentTypeSubscriber;
use OpenOrchestra\ModelInterface\ContentTypeEvents;

/**
 * Class LogContentTypeSubscriberTest
 */
class LogContentTypeSubscriberTest extends LogAbstractSubscriberTest
{
    protected $contentType;
    protected $contentTypeEvent;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
        $this->contentType = Phake::mock('OpenOrchestra\ModelBundle\Document\ContentType');
        $this->contentTypeEvent = Phake::mock('OpenOrchestra\ModelInterface\Event\ContentTypeEvent');
        Phake::when($this->contentTypeEvent)->getContentType()->thenReturn($this->contentType);

        $this->subscriber = new LogContentTypeSubscriber($this->logger);
    }

    /**
     * @return array
     */
    public function provideSubscribedEvent()
    {
        return array(
            array(ContentTypeEvents::CONTENT_TYPE_CREATE),
            array(ContentTypeEvents::CONTENT_TYPE_DELETE),
            array(ContentTypeEvents::CONTENT_TYPE_UPDATE),
        );
    }

    /**
     * test contentTypeCreation
     */
    public function testContentTypeCreation()
    {
        $this->subscriber->contentTypeCreation($this->contentTypeEvent);
        $this->assertEventLogged('open_orchestra_log.content_type.create', array(
            'content_type_id' => $this->contentType->getContentTypeId(),
        ));
    }

    /**
     * test contentTypeDelete
     */
    public function testContentTypeDelete()
    {
        $this->subscriber->contentTypeDelete($this->contentTypeEvent);
        $this->assertEventLogged('open_orchestra_log.content_type.delete', array(
            'content_type_id' => $this->contentType->getContentTypeId(),
            'content_type_name' => $this->contentType->getName()
        ));
    }

    /**
     * test contentTypeUpdate
     */
    public function testContentTypeUpdate()
    {
        $this->subscriber->contentTypeUpdate($this->contentTypeEvent);
        $this->assertEventLogged('open_orchestra_log.content_type.update', array(
            'content_type_id' => $this->contentType->getContentTypeId(),
            'content_type_name' => $this->contentType->getName()
        ));
    }
}
