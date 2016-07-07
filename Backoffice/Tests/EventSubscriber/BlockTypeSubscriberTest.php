<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\EventSubscriber\BlockTypeSubscriber;
use Symfony\Component\Form\FormEvents;

/**
 * Class BlockTypeSubscriberTest
 */
class BlockTypeSubscriberTest extends AbstractBaseTestCase
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
        $this->subscriber = new BlockTypeSubscriber($this->fixedParameters);
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
     * @param string $label
     * @param int    $blockPosition
     * @param string $expectedLabel
     * @param int    $count
     *
     * @dataProvider provideLabel
     */
    public function testPreSetData($label, $blockPosition, $expectedLabel, $count)
    {
        $component = 'fakeComponent';
        $config = Phake::mock('Symfony\Component\Form\FormConfigInterface');
        Phake::when($this->event)->getData()->thenReturn($this->block);
        Phake::when($this->block)->getLabel()->thenReturn($label);
        Phake::when($this->block)->getComponent()->thenReturn($component);
        Phake::when($this->form)->getConfig()->thenReturn($config);
        Phake::when($config)->getOption('blockPosition')->thenReturn($blockPosition);
        $this->subscriber->preSetData($this->event);
        Phake::verify($this->block, Phake::times($count))->setLabel($expectedLabel);
    }

    /**
     * @return array
     */
    public function provideLabel()
    {
        return array(
            array('', 1, 'fakeComponent #2', 1),
            array('', 2, 'fakeComponent #3', 1),
            array('', null, null, 0),
            array('label', null, null, 0),
        );
    }

    /**
     * Test submit
     */
    public function testSubmit()
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
