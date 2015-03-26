<?php

namespace OpenOrchestra\LogBundle\Tests\EventSubscriber;

use Phake;
use OpenOrchestra\LogBundle\EventSubscriber\LogTemplateSubscriber;
use OpenOrchestra\ModelInterface\TemplateEvents;

/**
 * Class LogTemplateSubscriber
 */
class LogTemplateSubscriberTest extends LogAbstractSubscriberTest
{
    protected $template;
    protected $templateEvent;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
        $this->template = Phake::mock('OpenOrchestra\ModelBundle\Document\Template');
        $this->templateEvent = Phake::mock('OpenOrchestra\ModelInterface\Event\TemplateEvent');
        Phake::when($this->templateEvent)->getTemplate()->thenReturn($this->template);

        $this->subscriber = new LogTemplateSubscriber($this->logger);
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
        $this->assertEventLogged('open_orchestra_log.template.create', array(
            'template_id' => $this->template->getTemplateId(),
            'template_name' => $this->template->getName()
        ));
    }

    /**
     * Test templateDelete
     */
    public function testTemplateDelete()
    {
        $this->subscriber->templateDelete($this->templateEvent);
        $this->assertEventLogged('open_orchestra_log.template.delete', array(
            'template_id' => $this->template->getTemplateId(),
            'template_name' => $this->template->getName()
        ));
    }

    /**
     * Test templateUpdate
     */
    public function testTemplateUpdate()
    {
        $this->subscriber->templateUpdate($this->templateEvent);
        $this->assertEventLogged('open_orchestra_log.template.update', array(
            'template_id' => $this->template->getTemplateId(),
            'template_name' => $this->template->getName()
        ));
    }
}
