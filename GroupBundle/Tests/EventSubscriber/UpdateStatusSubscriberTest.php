<?php

namespace OpenOrchestra\GroupBundle\Tests\EventSubscriber;

use Phake;
use OpenOrchestra\GroupBundle\EventSubscriber\UpdateStatusSubscriber;
use OpenOrchestra\ModelInterface\StatusEvents;

/**
 * Class UpdateStatusSubscriberTest
 */
class UpdateStatusSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UpdateStatusSubscriber
     */
    protected $subscriber;
    protected $authorizationChecker;
    protected $roleRepository;
    protected $statusableEvent;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $this->roleRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface');
        $this->statusableEvent =  Phake::mock('OpenOrchestra\ModelInterface\Event\StatusableEvent');
        $this->subscriber = new UpdateStatusSubscriber($this->authorizationChecker, $this->roleRepository);
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
    public function testEventSubscribed()
    {
        $this->assertArrayHasKey(StatusEvents::STATUS_CHANGE, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test updateStatus
     *
     * @param StatusInterface $status
     * @param array           $expectedParameters
     *
     * @dataProvider provideStatusableEvent
     */
    public function testUpdateStatus($document, $fromStatus, $role, $nbrCall)
    {
        Phake::when($this->statusableEvent)->getStatusableElement()->thenReturn($document);
        Phake::when($this->statusableEvent)->getFromStatus()->thenReturn($fromStatus);
        Phake::when($this->roleRepository)>findOneByFromStatusAndToStatus(Phake::anyParameters())->thenReturn($role);

        $this->subscriber->updateStatus($statusableEvent);

        Phake::verify($document, $nbrCall)->setStatus($fromStatus);
    }

    /**
     * @return array
     */
    public function provideStatusableEvent()
    {



        $document = $event->getStatusableElement();
        $fromStatus = $event->getFromStatus();
        $toStatus = $document->getStatus();
        $role = $this->roleRepository->findOneByFromStatusAndToStatus($fromStatus, $toStatus);
        if ($role && !$this->authorizationChecker->isGranted(array($role->getName()))) {
            $document->setStatus($fromStatus);
        }



        $data = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        Phake::when($data)->getStatus()->thenReturn($status);
        Phake::when($this->event)->getData()->thenReturn($data);

        $this->subscriber->postSetData($this->event);

        Phake::verify($this->form)->add('submit', 'submit', $expectedParameters);



        $status0 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status0)->isPublished()->thenReturn(true);

        $status1 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status1)->isPublished()->thenReturn(false);

        return array(
            array($status0, array('label' => 'open_orchestra_base.form.submit', 'attr' => array('class' => 'disabled'))),
            array($status1, array('label' => 'open_orchestra_base.form.submit', 'attr' => array('class' => 'submit_form'))),
            array(null, array('label' => 'open_orchestra_base.form.submit', 'attr' => array('class' => 'submit_form'))),
        );
    }
}
