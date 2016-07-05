<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\Backoffice\EventSubscriber\BlockFormTypeSubscriber;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\EventSubscriber\BlockTypeSubscriber;
use Symfony\Component\Form\FormEvents;

/**
 * Class BlockTypeSubscriberTest
 */
class BlockFormTypeSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var BlockTypeSubscriber
     */
    protected $subscriber;

    protected $form;
    protected $event;
    protected $block;
    protected $fixedParameters;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');

        $this->form = Phake::mock('Symfony\Component\Form\Form');
        Phake::when($this->form)->getData()->thenReturn($this->block);

        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);

        $this->fixedParameters = array('component', 'label', 'class', 'id', 'max_age');

        $this->subscriber = new BlockFormTypeSubscriber($this->fixedParameters);
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
        $this->assertArrayHasKey(FormEvents::SUBMIT, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(FormEvents::PRE_SET_DATA, $this->subscriber->getSubscribedEvents());
    }


    /**
     * Test submit
     */
    public function testsubmit()
    {
        $subForm1 = Phake::mock('Symfony\Component\Form\FormInterface');
        Phake::when($subForm1)->getData()->thenReturn('test');
        $subForm2 = Phake::mock('Symfony\Component\Form\FormInterface');
        Phake::when($subForm2)->getData()->thenReturn('sub');
        $subForm3 = Phake::mock('Symfony\Component\Form\FormInterface');
        Phake::when($subForm3)->getData()->thenReturn(array('1','2'));

        $subForms = array(
            'label' => $subForm1,
            'fakeAttribute' => $subForm2,
            'fakeArrayAttribute' => $subForm3,
        );
        Phake::when($this->form)->all()->thenReturn($subForms);

        $blockAttributes = array(
            'fakeAttribute' => 'sub',
            'fakeArrayAttribute' => array('1','2'),
        );

        $this->subscriber->submit($this->event);

        Phake::verify($this->block)->setAttributes($blockAttributes);
    }
}
