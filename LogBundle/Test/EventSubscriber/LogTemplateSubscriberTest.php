<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\LogBundle\EventSubscriber\LogTemplateSubscriber;
use PHPOrchestra\ModelInterface\TemplateEvents;

/**
 * Class LogTemplateSubscriber
 */
class LogTemplateSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogTemplateSubscriber
     */
    protected $subscriber;

    protected $logger;
    protected $template;
    protected $templateEvent;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->template = Phake::mock('PHPOrchestra\ModelBundle\Document\Template');
        $this->templateEvent = Phake::mock('PHPOrchestra\ModelInterface\Event\TemplateEvent');
        Phake::when($this->templateEvent)->getTemplate()->thenReturn($this->template);
        $this->logger = Phake::mock('Symfony\Bridge\Monolog\Logger');
        $this->subscriber = new LogTemplateSubscriber($this->logger);
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
            array(TemplateEvents::TEMPLATE_CREATE),
            array(TemplateEvents::TEMPLATE_DELETE),
            array(TemplateEvents::TEMPLATE_UPDATE),
        );
    }

    /**
     * Test templateCreate
     */
    public function testTemplateCreate()
    {
        $this->subscriber->templateCreate($this->templateEvent);
        $this->eventTest('php_orchestra_log.template.create');
    }

    /**
     * Test templateDelete
     */
    public function testTemplateDelete()
    {
        $this->subscriber->templateDelete($this->templateEvent);
        $this->eventTest('php_orchestra_log.template.delete');
    }

    /**
     * Test templateUpdate
     */
    public function testTemplateUpdate()
    {
        $this->subscriber->templateUpdate($this->templateEvent);
        $this->eventTest('php_orchestra_log.template.update');
    }

    /**
     * Test the templateEvent
     *
     * @param string $message
     */
    public function eventTest($message)
    {
        Phake::verify($this->templateEvent)->getTemplate();
        Phake::verify($this->logger)->info($message, array(
            'template_id' => $this->template->getTemplateId(),
            'template_name' => $this->template->getName()
        ));
    }
}
