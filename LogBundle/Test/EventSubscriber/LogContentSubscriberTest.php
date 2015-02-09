<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\LogBundle\EventSubscriber\LogContentSubscriber;
use PHPOrchestra\ModelInterface\ContentEvents;

/**
 * Class LogContentSubscriberTest
 */
class LogContentSubscriberTest extends LogAbstractSubscriberTest
{
    protected $content;
    protected $contentEvent;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
        $this->content = Phake::mock('PHPOrchestra\ModelBundle\Document\Content');
        $this->contentEvent = Phake::mock('PHPOrchestra\ModelInterface\Event\ContentEvent');
        Phake::when($this->contentEvent)->getContent()->thenReturn($this->content);

        $this->subscriber = new LogContentSubscriber($this->logger);
    }

    /**
     * @return array
     */
    public function provideSubscribedEvent()
    {
        return array(
            array(ContentEvents::CONTENT_CREATION),
            array(ContentEvents::CONTENT_DELETE),
            array(ContentEvents::CONTENT_DUPLICATE),
            array(ContentEvents::CONTENT_UPDATE),
        );
    }

    /**
     * Test contentCreation
     */
    public function testContentCreation()
    {
        $this->subscriber->contentCreation($this->contentEvent);
        $this->assertEventLogged('php_orchestra_log.content.create', array(
            'content_id' => $this->content->getContentId(),
        ));
    }

    /**
     * Test contentDelete
     */
    public function testContentDelete()
    {
        $this->subscriber->contentDelete($this->contentEvent);
        $this->assertEventLogged('php_orchestra_log.content.delete', array(
            'content_id' => $this->content->getContentId(),
            'content_name' => $this->content->getName(),
        ));
    }

    /**
     * Test contentUpdate
     */
    public function testContentUpdate()
    {
        $this->subscriber->contentUpdate($this->contentEvent);
        $this->assertEventLogged('php_orchestra_log.content.update', array(
            'content_id' => $this->content->getContentId(),
            'content_version' => $this->content->getVersion(),
            'content_language' => $this->content->getLanguage()
        ));
    }

    /**
     * Test contentDuplicate
     */
    public function testContentDuplicate()
    {
        $this->subscriber->contentDuplicate($this->contentEvent);
        $this->assertEventLogged('php_orchestra_log.content.duplicate', array(
            'content_id' => $this->content->getContentId(),
            'content_version' => $this->content->getVersion(),
            'content_language' => $this->content->getLanguage()
        ));
    }

    /**
     * Test contentChangeStatus
     */
    public function testContentChangeStatus()
    {
        $this->subscriber->contentChangeStatus($this->contentEvent);
        $this->assertEventLogged('php_orchestra_log.content.status', array(
            'content_id' => $this->content->getContentId(),
            'content_version' => $this->content->getVersion(),
            'content_language' => $this->content->getLanguage()
        ));
    }
}
