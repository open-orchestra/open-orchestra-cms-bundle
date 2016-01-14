<?php

namespace OpenOrchestra\BackofficeBundle\Tests\EventSubscriber;

use OpenOrchestra\BackofficeBundle\EventSubscriber\BoDirectionChildrenSubscriber;
use Phake;
use OpenOrchestra\BackofficeBundle\EventSubscriber\NodeChoiceSubscriber;
use Symfony\Component\Form\FormEvents;

/**
 * Class BoDirectionChildrenSubscriberTest
 */
class BoDirectionChildrenSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeChoiceSubscriber
     */
    protected $subscriber;

    protected $event;
    protected $form;
    protected $node;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($this->form)->add(Phake::anyParameters())->thenReturn($this->form);

        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);
        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');

        $this->subscriber = new BoDirectionChildrenSubscriber();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * Test subscribed event
     */
    public function testSubscribedEvent()
    {
        $this->assertArrayHasKey(FormEvents::PRE_SET_DATA, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test with new node
     */
    public function testPreSetDataWithNewNode()
    {
        Phake::when($this->node)->getId()->thenReturn("phakeId");
        Phake::when($this->event)->getData()->thenReturn($this->node);

        $this->subscriber->preSetData($this->event);
        Phake::verify($this->form)->add('boDirection', 'orchestra_direction', array(
            'label' => 'open_orchestra_backoffice.form.node.boDirection',
        ));
    }

    /**
     * Test with old node
     */
    public function testPreSetDataWithExistingNode()
    {
        Phake::when($this->node)->getId()->thenReturn(null);
        Phake::when($this->event)->getData()->thenReturn($this->node);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->form, Phake::never())->add(Phake::anyParameters());
    }
}
