<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use OpenOrchestra\Backoffice\EventSubscriber\SortableCollectionSubscriber;
use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class SortableCollectionSubscriberTest
 */
class SortableCollectionSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var SortableCollectionSubscriber
     */
    protected $subscriber;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->subscriber = new SortableCollectionSubscriber();
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
    public function testPreSubmit(FormInterface $form, array $data)
    {
        $event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($event)->getForm()->thenReturn($form);
        Phake::when($event)->getData()->thenReturn($data);

        $this->subscriber->preSubmit($event);
        Phake::verify($form)->setData(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideEvent()
    {
        $form = Phake::mock('Symfony\Component\Form\FormInterface');
        Phake::when($form)->getData()->thenReturn(new ArrayCollection(array(
            'order0' => true,
            'order1' => true,
            'order2' => true,
            'order3' => true,
       )));
       $data = array(
            'order3' => true,
            'order2' => true,
            'order1' => true,
            'order0' => true,
       );

       return array(
           array($form, $data),
       );
    }
}
