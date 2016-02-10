<?php

namespace OpenOrchestra\LogBundle\Tests\EventSubscriber;

use OpenOrchestra\LogBundle\EventSubscriber\LogTemplateFlexSubscriber;
use OpenOrchestra\ModelInterface\TemplateFlexEvents;
use Phake;

/**
 * Class LogTemplateFlexSubscriberTest
 */
class LogTemplateFlexSubscriberTest extends LogAbstractSubscriberTest
{
    protected $template;
    protected $templateEvent;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
        $this->template = Phake::mock('OpenOrchestra\ModelBundle\Document\TemplateFlex');
        $this->templateEvent = Phake::mock('OpenOrchestra\ModelInterface\Event\TemplateFlexEvent');
        Phake::when($this->templateEvent)->getTemplate()->thenReturn($this->template);

        $this->subscriber = new LogTemplateFlexSubscriber($this->logger);
    }

    /**
     * @return array
     */
    public function provideSubscribedEvent()
    {
        return array(
            array(TemplateFlexEvents::TEMPLATE_FLEX_CREATE),
            array(TemplateFlexEvents::TEMPLATE_FLEX_DELETE),
            array(TemplateFlexEvents::TEMPLATE_FLEX_UPDATE),
        );
    }

    /**
     * Test templateCreate
     */
    public function testTemplateCreate()
    {
        $this->subscriber->templateCreate($this->templateEvent);
        $this->assertEventLogged('open_orchestra_log.template_flex.create', array(
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
        $this->assertEventLogged('open_orchestra_log.template_flex.delete', array(
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
        $this->assertEventLogged('open_orchestra_log.template_flex.update', array(
            'template_id' => $this->template->getTemplateId(),
            'template_name' => $this->template->getName()
        ));
    }
}
