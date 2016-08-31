<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\Backoffice\EventSubscriber\UpdateStatusSubscriber;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\StatusEvents;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Model\IsStatusableInterface;
use Phake;

/**
 * Test UpdateStatusSubscriberTest
 */
class UpdateStatusSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var UpdateStatusSubscriber
     */
    protected $subscriber;

    protected $event;
    protected $toStatus;
    protected $authorizationChangeManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->toStatus = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $this->event = Phake::mock('OpenOrchestra\ModelInterface\Event\StatusableEvent');
        Phake::when($this->event)->getToStatus()->thenReturn($this->toStatus);
        $this->authorizationChangeManager = Phake::mock('OpenOrchestra\BackofficeBundle\StrategyManager\AuthorizeStatusChangeManager');

        $this->subscriber = new UpdateStatusSubscriber($this->authorizationChangeManager);
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
    public function testSubscribedEvents()
    {
        $this->assertArrayHasKey(StatusEvents::STATUS_CHANGE, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test when ok
     * @param StatusableInterface $document
     * @param int                 $nbrCall
     *
     * @dataProvider provideDocument
     */
    public function testUpdateStatusWhenOk(StatusableInterface $document, $nbrCall)
    {
        Phake::when($this->authorizationChangeManager)->isGranted(Phake::anyParameters())->thenReturn(true);
        Phake::when($this->event)->getStatusableElement()->thenReturn($document);

        $this->subscriber->updateStatus($this->event);

        Phake::verify($document, Phake::times($nbrCall))->setStatus($this->toStatus);
    }

    /**
     * Test when not ok
     * @param StatusableInterface $document
     * @param int                 $nbrCall
     *
     * @dataProvider provideDocument
     */
    public function testUpdateStatusWhenNotOk(StatusableInterface $document, $nbrCall)
    {
        Phake::when($this->authorizationChangeManager)->isGranted(Phake::anyParameters())->thenReturn(false);
        Phake::when($this->event)->getStatusableElement()->thenReturn($document);

        $this->setExpectedException('OpenOrchestra\Backoffice\Exception\StatusChangeNotGrantedException');
        $this->subscriber->updateStatus($this->event);

        Phake::verify($document, Phake::never())->setStatus(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideDocument()
    {
        $document0 = Phake::mock('OpenOrchestra\Backoffice\Tests\EventSubscriber\FakeDocument0');
        Phake::when($document0)->isStatusable()->thenReturn(true);

        $document1 = Phake::mock('OpenOrchestra\Backoffice\Tests\EventSubscriber\FakeDocument0');
        Phake::when($document1)->isStatusable()->thenReturn(false);

        $document2 = Phake::mock('OpenOrchestra\Backoffice\Tests\EventSubscriber\FakeDocument1');

        return array(
            array($document0, 1),
            array($document1, 0),
            array($document2, 1),
        );
    }

}

abstract class FakeDocument0 implements IsStatusableInterface, StatusableInterface
{
}

abstract class FakeDocument1 implements StatusableInterface
{
}
