<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use OpenOrchestra\Backoffice\EventSubscriber\SortableCollectionSubscriber;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Form;

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
     * @param mixed $formData
     * @param array $data
     *
     * @dataProvider provideEvent
     */
    public function testPreSubmit($formData, array $data, array $expectedOrder)
    {
        $config = Phake::mock('Symfony\Component\Form\FormConfigInterface');
        $eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        Phake::when($config)->getEventDispatcher()->thenReturn($eventDispatcher);
        Phake::when($config)->getModelTransformers()->thenReturn(array());

        $form = new Form($config);
        $form->setData($formData);

        $event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($event)->getForm()->thenReturn($form);
        Phake::when($event)->getData()->thenReturn($data);

        $this->subscriber->preSubmit($event);

        $newData = $form->getData();

        if (!is_array($newData)) {
            $newData = $newData->toArray();
        }

        $this->assertEquals($expectedOrder, array_keys($newData));
    }

    /**
     * @return array
     */
    public function provideEvent()
    {
        $formData0 = new ArrayCollection(array(
            'order0' => true,
            'order1' => true,
            'order2' => true,
            'order3' => true,
       ));
       $data0 = array(
            'order3' => true,
            'order2' => true,
            'order1' => true,
            'order0' => true,
       );

        $formData1 = array(
            'order0' => true,
            'order1' => true,
            'order2' => true,
            'order3' => true,
       );
       $data1 = array(
            'order3' => true,
            'order2' => true,
            'order1' => true,
            'order0' => true,
       );

       $expectedOrder = array(
           'order3',
           'order2',
           'order1',
           'order0',
       );

       return array(
           array($formData0, $data0, $expectedOrder),
           array($formData1, $data1, $expectedOrder),
       );
    }
}
