<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\Backoffice\EventSubscriber\CreateMandatoryNodesSubscriber;
use OpenOrchestra\ModelInterface\SiteEvents;
use Phake;

/**
 * Class CreateMandatoryNodesSubscriberTest
 */
class CreateMandatoryNodesSubscriberTest extends \PHPUnit_Framework_TestCase
{
    protected $objectManager;
    protected $nodeManager;
    protected $translator;
    /**
     * @var CreateMandatoryNodesSubscriber
     */
    protected $subscriber;
    protected $siteEvent;
    protected $site;
    protected $statusRepository;

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
        $this->statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');
        Phake::when($this->statusRepository)->findOneByTranslationState()->thenReturn(Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface'));
        Phake::when($this->nodeManager)->createNewErrorNode(Phake::anyParameters())->thenReturn(Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface'));
        Phake::when($this->nodeManager)->createRootNode(Phake::anyParameters())->thenReturn(Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface'));
        Phake::when($this->nodeManager)->initializeAreasNode(Phake::anyParameters())->thenReturn(Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface'));
        $this->subscriber = new CreateMandatoryNodesSubscriber(
            $this->nodeManager,
            $this->statusRepository,
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
     * Test create mandatory nodes
     * @param array $languages
     * @param int   $count
     *
     * @dataProvider provideLanguages
     */
    public function testCreateMandatoryNodes(array $languages, $count)
    {
        Phake::when($this->site)->getLanguages()->thenReturn($languages);

        $this->subscriber->createMandatoryNodes($this->siteEvent);

        Phake::verify($this->nodeManager, Phake::times($count))->createRootNode(Phake::anyParameters());
        Phake::verify($this->nodeManager, Phake::times($count * 2 ))->createNewErrorNode(Phake::anyParameters());
        Phake::verify($this->objectManager, Phake::times($count * 3))->persist(Phake::anyParameters());
        Phake::verify($this->objectManager)->flush();
    }

    /**
     * @return array
     */
    public function provideLanguages()
    {
        return array(
                array(array(), 0),
                array(array('en'), 1),
                array(array('en', 'fr'), 2),
        );
    }
}
