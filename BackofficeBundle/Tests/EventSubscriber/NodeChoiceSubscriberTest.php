<?php

namespace OpenOrchestra\BackofficeBundle\Tests\EventSubscriber;

use Phake;
use OpenOrchestra\BackofficeBundle\EventSubscriber\NodeChoiceSubscriber;
use Symfony\Component\Form\FormEvents;

/**
 * Class NodeChoiceSubscriberTest
 */
class NodeChoiceSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeChoiceSubscriber
     */
    protected $subscriber;

    protected $nodeManager;
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

        $this->nodeManager = Phake::mock('OpenOrchestra\BackofficeBundle\Manager\NodeManager');
        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');

        $this->subscriber = new NodeChoiceSubscriber($this->nodeManager);
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
        $this->assertArrayHasKey(FormEvents::PRE_SUBMIT, $this->subscriber->getSubscribedEvents());
        $events = $this->subscriber->getSubscribedEvents();
        $this->assertSame(array('preSubmit', 100), $events[FormEvents::PRE_SUBMIT]);
    }

    /**
     * Test with new node
     */
    public function testPreSetDataWithNewNode()
    {
        Phake::when($this->node)->getId()->thenReturn(null);
        Phake::when($this->event)->getData()->thenReturn($this->node);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->form)->add('nodeSource', 'oo_node_choice', array(
            'required' => false,
            'mapped' => false,
            'label' => 'open_orchestra_backoffice.form.node.node_source'
        ));
    }

    /**
     * Test with old node
     */
    public function testPreSetDataWithExistingNode()
    {
        Phake::when($this->node)->getId()->thenReturn('nodeId');
        Phake::when($this->event)->getData()->thenReturn($this->node);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->form, Phake::never())->add(Phake::anyParameters());
    }

    /**
     * Test presubmit on new node
     */
    public function testPreSubmitWithNewNode()
    {
        $nodeSourceId = 'root';
        Phake::when($this->node)->getId()->thenReturn(null);
        Phake::when($this->form)->getData()->thenReturn($this->node);
        Phake::when($this->event)->getData()->thenReturn(array('nodeSource' => $nodeSourceId));

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->nodeManager)->hydrateNodeFromNodeId($this->node, $nodeSourceId);
    }

    /**
     * Test presubmit on old node
     */
    public function testPreSubmitWithOldNode()
    {
        $nodeSourceId = 'root';
        Phake::when($this->node)->getId()->thenReturn($nodeSourceId);
        Phake::when($this->form)->getData()->thenReturn($this->node);
        Phake::when($this->event)->getData()->thenReturn(array('nodeSource' => $nodeSourceId));

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->nodeManager, Phake::never())->hydrateNodeFromNodeId(Phake::anyParameters());
    }

    /**
     * Test presubmit with no source
     */
    public function testPreSubmitWithNoSource()
    {
        Phake::when($this->node)->getId()->thenReturn(null);
        Phake::when($this->form)->getData()->thenReturn($this->node);
        Phake::when($this->event)->getData()->thenReturn(array());

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->nodeManager, Phake::never())->hydrateNodeFromNodeId(Phake::anyParameters());
    }
}
