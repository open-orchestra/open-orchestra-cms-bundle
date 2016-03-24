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
    protected $repositoryTemplate;
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
        $this->repositoryTemplate = Phake::mock('OpenOrchestra\ModelInterface\Repository\TemplateRepositoryInterface');

        $this->subscriber = new CreateRootNodeSubscriber(
            $this->nodeManager,
            $this->repositoryTemplate,
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
     * @param string $fakeTemplateId
     * @param int    $countCreateRootNode
     *
     * @dataProvider provideTemplateIdAndCountCreateRootNode
     */
    public function testCreateRootNode($fakeTemplateId, $countCreateRootNode)
    {
        $template = Phake::mock('OpenOrchestra\ModelInterface\Model\TemplateInterface');
        Phake::when($this->repositoryTemplate)->findOneByTemplateId(Phake::anyParameters())->thenReturn($template);
        Phake::when($this->siteEvent)->getRootNodeTemplateId()->thenReturn($fakeTemplateId);

        $this->subscriber->createRootNode($this->siteEvent);

        Phake::verify($this->nodeManager, Phake::times($countCreateRootNode))->createRootNode(Phake::anyParameters());
        Phake::verify($this->objectManager, Phake::times($countCreateRootNode))->persist(Phake::anyParameters());
        Phake::verify($this->objectManager, Phake::times($countCreateRootNode))->flush();
    }

    /**
     * @return array
     */
    public function provideTemplateIdAndCountCreateRootNode()
    {
        return array(
            "with template id" => array('fakeTemplateid', 1),
            "no template id "  => array(null, 0)
        );
    }
}
