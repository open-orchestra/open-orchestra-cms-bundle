<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\LogBundle\EventSubscriber\LogKeywordSubscriber;
use PHPOrchestra\ModelInterface\KeywordEvents;

/**
 * Class LogKeywordSubscriberTest
 */
class LogKeywordSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogKeywordSubscriber
     */
    protected $subscriber;

    protected $logger;
    protected $keyword;
    protected $keywordEvent;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->keyword = Phake::mock('PHPOrchestra\ModelBundle\Document\Keyword');
        $this->keywordEvent = Phake::mock('PHPOrchestra\ModelInterface\Event\KeywordEvent');
        Phake::when($this->keywordEvent)->getKeyword()->thenReturn($this->keyword);
        $this->logger = Phake::mock('Symfony\Bridge\Monolog\Logger');
        $this->subscriber = new LogKeywordSubscriber($this->logger);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * @param string $eventName
     *
     * @dataProvider provideSubscribedEvent
     */
    public function testEventSubscribed($eventName)
    {
        $this->assertArrayHasKey($eventName, $this->subscriber->getSubscribedEvents());
    }

    /**
     * @return array
     */
    public function provideSubscribedEvent()
    {
        return array(
            array(KeywordEvents::KEYWORD_CREATE),
            array(KeywordEvents::KEYWORD_DELETE),
        );
    }

    /**
     * Test keywordCreate
     */
    public function testKeywordCreate()
    {
        $this->subscriber->keywordCreate($this->keywordEvent);
        $this->eventTest();
    }

    /**
     * Test keywordDelete
     */
    public function testKeywordDelete()
    {
        $this->subscriber->keywordDelete($this->keywordEvent);
        $this->eventTest();
    }

    /**
     * Test the keywordEvent
     */
    public function eventTest()
    {
        Phake::verify($this->keywordEvent)->getKeyword();
        Phake::verify($this->logger)->info(Phake::anyParameters());
        Phake::verify($this->keyword)->getLabel();
    }
}
