<?php

namespace OpenOrchestra\BackofficeBundle\Tests\EventSubscriber;

use OpenOrchestra\BackofficeBundle\EventSubscriber\CreateTransverseNodeSubscriber;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\SiteEvents;
use Phake;

/**
 * Class CreateTransverseNodeSubscriberTest
 */
class CreateTransverseNodeSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CreateTransverseNodeSubscriber
     */
    protected $subscriber;

    protected $nodeRepository;
    protected $objectManager;
    protected $nodeManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->objectManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $this->nodeManager = Phake::mock('OpenOrchestra\BackofficeBundle\Manager\NodeManager');

        $this->subscriber = new CreateTransverseNodeSubscriber($this->nodeRepository, $this->nodeManager, $this->objectManager);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * Test subscribed events
     */
    public function testSubscribedEvent()
    {
        $this->assertArrayHasKey(SiteEvents::SITE_CREATE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(SiteEvents::SITE_UPDATE, $this->subscriber->getSubscribedEvents());
    }

    /**
     * @param array  $languages
     * @param string $siteId
     *
     * @dataProvider provideLanguageAndSiteId
     */
    public function testOnSiteCreation(array $languages, $siteId)
    {
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->nodeManager)->createTransverseNode(Phake::anyParameters())->thenReturn($node);

        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site)->getLanguages()->thenReturn($languages);
        Phake::when($site)->getSiteId()->thenReturn($siteId);
        $event = Phake::mock('OpenOrchestra\ModelInterface\Event\SiteEvent');
        Phake::when($event)->getSite()->thenReturn($site);

        $this->subscriber->onSiteCreation($event);

        Phake::verify($this->nodeManager, Phake::times(count($languages)))->createTransverseNode(Phake::anyParameters());
        Phake::verify($this->objectManager, Phake::times(count($languages)))->persist($node);
        Phake::verify($this->objectManager, Phake::times(count($languages)))->flush($node);
        foreach ($languages as $language) {
            Phake::verify($this->nodeManager)->createTransverseNode($language, $siteId);
        }
        Phake::verifyNoInteraction($this->nodeRepository);
    }

    /**
     * @return array
     */
    public function provideLanguageAndSiteId()
    {
        return array(
            array(array('en'), '2'),
            array(array('en', 'fr'), '4'),
            array(array('en', 'fr', 'de'), '1'),
        );
    }

    /**
     * @param array  $languages
     * @param string $siteId
     *
     * @dataProvider provideLanguageAndSiteId
     */
    public function testOnSiteUpdateWithNoNodeToUpdate($languages, $siteId)
    {
        foreach ($languages as $language) {
            $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
            Phake::when($node)->getLanguage()->thenReturn($language);
            $nodes[] = $node;
        }
        Phake::when($this->nodeRepository)->findByNodeTypeAndSite(Phake::anyParameters())->thenReturn($nodes);

        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site)->getLanguages()->thenReturn($languages);
        Phake::when($site)->getSiteId()->thenReturn($siteId);
        $event = Phake::mock('OpenOrchestra\ModelInterface\Event\SiteEvent');
        Phake::when($event)->getSite()->thenReturn($site);

        $this->subscriber->onSiteUpdate($event);

        Phake::verifyNoInteraction($this->objectManager);
        Phake::verifyNoInteraction($this->nodeManager);
        Phake::verify($this->nodeRepository)->findByNodeTypeAndSite(NodeInterface::TYPE_TRANSVERSE, $siteId);
    }

    /**
     * @param array  $languages
     * @param string $siteId
     * @param array  $nodeLanguage
     * @param array  $newLanguages
     *
     * @dataProvider provideLanguageAndSiteIdAndTransverseNodeLanguage
     */
    public function testOnSiteUpdateWithNodeToUpdate(array $languages, $siteId, array $nodeLanguage, array $newLanguages)
    {
        foreach ($nodeLanguage as $language) {
            $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
            Phake::when($node)->getLanguage()->thenReturn($language);
            $nodes[] = $node;
        }
        Phake::when($this->nodeRepository)->findByNodeTypeAndSite(Phake::anyParameters())->thenReturn($nodes);

        $newNode = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->nodeManager)->createNewLanguageNode(Phake::anyParameters())->thenReturn($newNode);

        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site)->getLanguages()->thenReturn($languages);
        Phake::when($site)->getSiteId()->thenReturn($siteId);
        $event = Phake::mock('OpenOrchestra\ModelInterface\Event\SiteEvent');
        Phake::when($event)->getSite()->thenReturn($site);

        $this->subscriber->onSiteUpdate($event);

        Phake::verify($this->nodeRepository)->findByNodeTypeAndSite(NodeInterface::TYPE_TRANSVERSE, $siteId);
        foreach ($newLanguages as $newLanguage) {
            Phake::verify($this->nodeManager)->createNewLanguageNode(end($nodes), $newLanguage);
        }

        Phake::verify($this->objectManager, Phake::times(count($newLanguages)))->flush($newNode);
    }

    /**
     * @return array
     */
    public function provideLanguageAndSiteIdAndTransverseNodeLanguage()
    {
        return array(
            array(array('en', 'fr'), '2', array('en'), array('fr')),
            array(array('en', 'fr', 'de'), '5', array('en', 'fr'), array('de')),
            array(array('en', 'fr', 'de'), '5', array('en'), array('fr', 'de')),
        );
    }
}
