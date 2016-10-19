<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\Backoffice\EventSubscriber\CreateRootNodeSubscriber;
use OpenOrchestra\ModelInterface\SiteEvents;
use Phake;

/**
 * Class CreateNodeRootSubscriberTest
 */
class CreateNodeRootSubscriberTest extends \PHPUnit_Framework_TestCase
{
    protected $objectManager;
    protected $nodeManager;
    protected $translator;
    /**
     * @var CreateRootNodeSubscriber
     */
    protected $subscriber;
    protected $siteEvent;
    protected $site;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        $this->siteEvent = Phake::mock('OpenOrchestra\ModelInterface\Event\SiteEvent');
        Phake::when($this->siteEvent)->getSite()->thenReturn($this->site);
        $this->objectManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');
        $this->nodeManager = Phake::mock('OpenOrchestra\Backoffice\Manager\NodeManager');
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');

        $this->subscriber = new CreateRootNodeSubscriber(
            $this->nodeManager,
            $this->objectManager,
            $this->translator
        );
    }

    /**
     * Test event subscribed
     */
    public function testEventSubscribed()
    {
        $this->assertArrayHasKey(SiteEvents::SITE_CREATE, $this->subscriber->getSubscribedEvents());
    }

    /**
     * @param string $fakeTemplate
     * @param int    $countCreateRootNode
     *
     * @dataProvider provideTemplateAndCountCreateRootNode
     */
    public function testCreateRootNode($fakeTemplate, $countCreateRootNode)
    {
        Phake::when($this->siteEvent)->getRootNodeTemplate()->thenReturn($fakeTemplate);

        $this->subscriber->createRootNode($this->siteEvent);

        Phake::verify($this->nodeManager, Phake::times($countCreateRootNode))->createRootNode(Phake::anyParameters());
        Phake::verify($this->objectManager, Phake::times($countCreateRootNode))->persist(Phake::anyParameters());
        Phake::verify($this->objectManager, Phake::times($countCreateRootNode))->flush();
    }

    /**
     * @return array
     */
    public function provideTemplateAndCountCreateRootNode()
    {
        return array(
            "with template id" => array('fakeTemplateid', 1)
        );
    }
}
