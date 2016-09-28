<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\EventSubscriber\UpdateReportListSubscriber;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Model\ReportableInterface;
use OpenOrchestra\ModelInterface\Model\ReportInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;

/**
 * Test UpdateReportListSubscriberTest
 */
class UpdateReportListSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var UpdateReportListSubscriber
     */
    protected $subscriber;

    protected $tokenManager;
    protected $objectManager;
    protected $reportClass;
    protected $token;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->tokenManager = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
        $this->objectManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');
        $this->reportClass = 'OpenOrchestra\Backoffice\Tests\EventSubscriber\FakeReportClass';

        $this->subscriber = new UpdateReportListSubscriber($this->tokenManager, $this->objectManager, $this->reportClass);

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
    }

    /**
     * @param mixed $document
     * @param TokenInterface|null $user
     * @param integer            $nbrUpdate
     *
     * @dataProvider provideDocument
     */
    public function testAddReport($document, $token, $nbrUpdate)
    {
        Phake::when($this->tokenManager)->getToken()->thenReturn($token);

        $event = Phake::mock('OpenOrchestra\ModelInterface\Event\ContentEvent');
        Phake::when($event)->getContent()->thenReturn($document);

        $this->subscriber->addReport($event);

        Phake::verify($this->objectManager, Phake::times($nbrUpdate))->flush(Phake::anyParameters());

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
            //array(new \stdClass(), $token1, 0),
            //array(Phake::mock('OpenOrchestra\Backoffice\Tests\EventSubscriber\FakeReportableInterfaceClass'), $token0, 0),
            array(Phake::mock('OpenOrchestra\Backoffice\Tests\EventSubscriber\FakeReportableInterfaceClass'), $token1, 1),
        );
    }
}

class FakeReportableInterfaceClass implements ReportableInterface
{
    /**
     * Add report
     *
     * @param ReportInterface $report
     */
    public function addReport(ReportInterface $report){}
}

class FakeReportClass implements ReportInterface
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
}
