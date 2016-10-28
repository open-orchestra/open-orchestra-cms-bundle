<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use OpenOrchestra\Backoffice\EventSubscriber\ContentTypeStatusableSubscriber;

/**
 * Class ContentTypeStatusableSubscriberTest
 */
class ContentTypeStatusableSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var ContentTypeStatusableSubscriber
     */
    protected $subscriber;
    protected $contentRepository;
    protected $statusRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->contentRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface');
        $this->statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');
        Phake::when($this->statusRepository)->findOneByOutOfWorkflow()->thenReturn(Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface'));
        Phake::when($this->statusRepository)->findOneByInitial()->thenReturn(Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface'));

        $this->subscriber = new ContentTypeStatusableSubscriber(
            $this->contentRepository,
            $this->statusRepository
        );

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
        $this->assertArrayHasKey(FormEvents::PRE_SUBMIT, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test preSubmit
     * @param FormEvent $event
     * @param int       $isInitial
     * @param int       $isOutOfWorkflow
     * @param int       $isUdpated
     *
     * @dataProvider provideEvent
     */
    public function testPreSubmit(FormEvent $event, $isInitial, $isOutOfWorkflow, $isUpdated)
    {
        $this->subscriber->preSubmit($event);

        Phake::verify($this->statusRepository, Phake::times($isOutOfWorkflow))->findOneByOutOfWorkflow();
        Phake::verify($this->statusRepository, Phake::times($isInitial))->findOneByInitial();
        Phake::verify($this->contentRepository, Phake::times($isUpdated))->updateStatusByContentType(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideEvent()
    {
        $event0 = Phake::mock('Symfony\Component\Form\FormEvent');
        $form0 = Phake::mock('Symfony\Component\Form\FormInterface');
        $contentType0 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($contentType0)->isDefiningStatusable()->thenReturn(false);
        Phake::when($contentType0)->getContentTypeId()->thenReturn('fakeContentTypeId');
        Phake::when($form0)->getData()->thenReturn($contentType0);
        Phake::when($event0)->getForm()->thenReturn($form0);
        Phake::when($event0)->getData()->thenReturn(array('definingStatusable' => 1));

        $event1 = Phake::mock('Symfony\Component\Form\FormEvent');
        $form1 = Phake::mock('Symfony\Component\Form\FormInterface');
        $contentType1 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($contentType1)->isDefiningStatusable()->thenReturn(true);
        Phake::when($contentType1)->getContentTypeId()->thenReturn('fakeContentTypeId');
        Phake::when($form1)->getData()->thenReturn($contentType1);
        Phake::when($event1)->getForm()->thenReturn($form1);
        Phake::when($event1)->getData()->thenReturn(array('definingStatusable' => 0));

        $event2 = Phake::mock('Symfony\Component\Form\FormEvent');
        $form2 = Phake::mock('Symfony\Component\Form\FormInterface');
        Phake::when($form2)->getData()->thenReturn(new \stdClass());
        Phake::when($event2)->getForm()->thenReturn($form2);

        $event3 = Phake::mock('Symfony\Component\Form\FormEvent');
        $form3 = Phake::mock('Symfony\Component\Form\FormInterface');
        $contentType3 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($contentType3)->isDefiningStatusable()->thenReturn(true);
        Phake::when($contentType3)->getContentTypeId()->thenReturn('fakeContentTypeId');
        Phake::when($form3)->getData()->thenReturn($contentType3);
        Phake::when($event3)->getForm()->thenReturn($form3);
        Phake::when($event3)->getData()->thenReturn(array());


        return array(
            array($event0, 1, 1, 1),
            array($event1, 0, 1, 1),
            array($event2, 0, 0, 0),
            array($event3, 0, 0, 0),
        );
    }
}
