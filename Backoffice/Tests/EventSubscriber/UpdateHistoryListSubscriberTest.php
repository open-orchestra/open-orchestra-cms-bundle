<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\EventSubscriber\UpdateHistoryListSubscriber;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Model\HistorisableInterface;
use OpenOrchestra\ModelInterface\Model\HistoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Test UpdateHistoryListSubscriberTest
 */
class UpdateHistoryListSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var UpdateHistoryListSubscriber
     */
    protected $subscriber;

    protected $tokenManager;
    protected $objectManager;
    protected $historyClass;
    protected $token;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->tokenManager = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
        $this->objectManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');
        $this->historyClass = 'OpenOrchestra\Backoffice\Tests\EventSubscriber\FakeHistoryClass';

        $this->subscriber = new UpdateHistoryListSubscriber($this->tokenManager, $this->objectManager, $this->historyClass);

    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * Test event subscribed
     */
    public function testEventSubscribed()
    {
        $this->assertArrayHasKey(ContentEvents::CONTENT_UPDATE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(ContentEvents::CONTENT_CREATION, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(ContentEvents::CONTENT_DELETE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(ContentEvents::CONTENT_RESTORE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(ContentEvents::CONTENT_DUPLICATE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(ContentEvents::CONTENT_CHANGE_STATUS, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(NodeEvents::PATH_UPDATED, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(NodeEvents::NODE_UPDATE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(NodeEvents::NODE_UPDATE_BLOCK, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(NodeEvents::NODE_UPDATE_BLOCK_POSITION, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(NodeEvents::NODE_CREATION, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(NodeEvents::NODE_DELETE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(NodeEvents::NODE_RESTORE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(NodeEvents::NODE_DUPLICATE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(NodeEvents::NODE_ADD_LANGUAGE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(NodeEvents::NODE_DELETE_BLOCK, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(NodeEvents::NODE_DELETE_AREA, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(NodeEvents::NODE_UPDATE_AREA, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(NodeEvents::NODE_CHANGE_STATUS, $this->subscriber->getSubscribedEvents());
    }

    /**
     * @param mixed               $document
     * @param TokenInterface|null $user
     * @param integer             $nbrUpdate
     *
     * @dataProvider provideDocument
     */
    public function testAddHistory($document, $token, $nbrUpdate)
    {
        $this->addContentHistory('addContentUpdateHistory', $document, $token);
        $this->addContentHistory('addContentCreationHistory', $document, $token);
        $this->addContentHistory('addContentDeleteHistory', $document, $token);
        $this->addContentHistory('addContentRestoreHistory', $document, $token);
        $this->addContentHistory('addContentDuplicateHistory', $document, $token);
        $this->addContentHistory('addContentChangeStatusHistory', $document, $token);

        $this->addNodeHistory('addPathUpdatedHistory', $document, $token);
        $this->addNodeHistory('addNodeUpdateHistory', $document, $token);
        $this->addNodeHistory('addNodeUpdateBlockHistory', $document, $token);
        $this->addNodeHistory('addNodeUpdateBlockPositionHistory', $document, $token);
        $this->addNodeHistory('addNodeUpdateBlockPositionHistory', $document, $token);
        $this->addNodeHistory('addNodeCreationHistory', $document, $token);
        $this->addNodeHistory('addNodeDeleteHistory', $document, $token);
        $this->addNodeHistory('addNodeRestoreHistory', $document, $token);
        $this->addNodeHistory('addNodeDuplicateHistory', $document, $token);
        $this->addNodeHistory('addNodeAddLanguageHistory', $document, $token);
        $this->addNodeHistory('addNodeDeleteBlockHistory', $document, $token);
        $this->addNodeHistory('addNodeDeleteAreaHistory', $document, $token);
        $this->addNodeHistory('addNodeUpdateAreaHistory', $document, $token);
        $this->addNodeHistory('addNodeChangeStatusHistory', $document, $token);

        Phake::verify($this->objectManager, Phake::times($nbrUpdate * 20))->flush();
    }

    /**
     * @param string              $method
     * @param mixed               $document
     * @param TokenInterface|null $user
     * @param integer             $nbrUpdate
     */
    protected function addContentHistory($method, $document, $token)
    {
        Phake::when($this->tokenManager)->getToken()->thenReturn($token);

        $event = Phake::mock('OpenOrchestra\ModelInterface\Event\ContentEvent');
        Phake::when($event)->getContent()->thenReturn($document);

        $this->subscriber->$method($event);

    }

    /**
     * @param string              $method
     * @param mixed               $document
     * @param TokenInterface|null $user
     * @param integer             $nbrUpdate
     */
    protected function addNodeHistory($method, $document, $token)
    {
        Phake::when($this->tokenManager)->getToken()->thenReturn($token);

        $event = Phake::mock('OpenOrchestra\ModelInterface\Event\NodeEvent');
        Phake::when($event)->getNode()->thenReturn($document);

        $this->subscriber->$method($event);
    }

    /**
     * @return array
     */
    public function provideDocument()
    {
        $token0 = null;

        $token1 = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        Phake::when($token1)->getUser()->thenReturn(Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface'));

        return array(
            array(new \stdClass(), $token1, 0),
            array(Phake::mock('OpenOrchestra\Backoffice\Tests\EventSubscriber\FakeHistorisableInterfaceClass'), $token0, 0),
            array(Phake::mock('OpenOrchestra\Backoffice\Tests\EventSubscriber\FakeHistorisableInterfaceClass'), $token1, 1),
        );
    }
}

class FakeHistorisableInterfaceClass implements HistorisableInterface
{
    /**
     * Add history
     *
     * @param HistoryInterface $history
     */
    public function addHistory(HistoryInterface $history){}
}

class FakeHistoryClass implements HistoryInterface
{
    /**
     * Set user
     *
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user){}

    /**
     * Get user
     *
     * @return UserInterface $user
     */
    public function getUser(){}

    /**
     * Sets updatedAt.
     *
     * @param  \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt){}

    /**
     * Returns updatedAt.
     *
     * @return \Datetime
     */
    public function getUpdatedAt(){}

    /**
     * Sets eventType
     *
     * @param  string $eventType
     */
    public function setEventType($eventType){}

    /**
     * Returns eventType
     *
     * @return string
     */
    public function getEventType(){}
}
