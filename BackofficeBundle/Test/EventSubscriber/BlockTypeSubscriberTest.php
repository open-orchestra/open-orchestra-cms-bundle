<?php

namespace PHPOrchestra\BackofficeBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\BackofficeBundle\EventSubscriber\BlockTypeSubscriber;
use Symfony\Component\Form\FormEvents;

/**
 * Class BlockTypeSubscriberTest
 */
class BlockTypeSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BlockTypeSubscriber
     */
    protected $subscriber;

    protected $event;
    protected $form;
    protected $block;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->block = Phake::mock('PHPOrchestra\ModelBundle\Model\BlockInterface');

        $this->form = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($this->form)->add(Phake::anyParameters())->thenReturn($this->form);

        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);

        $this->subscriber = new BlockTypeSubscriber();
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
        $this->assertArrayHasKey(FormEvents::PRE_SET_DATA, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(FormEvents::PRE_SUBMIT, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test with no data
     */
    public function testPreSetDataWithNoAttributes()
    {
        Phake::when($this->event)->getData()->thenReturn($this->block);

        $attributes = array();
        Phake::when($this->block)->getAttributes()->thenReturn($attributes);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->form, Phake::never())->add(Phake::anyParameters());
    }

    /**
     * Test with string or int attributes
     */
    public function testPreSetDataWithMultipleStringAttributes()
    {
        Phake::when($this->event)->getData()->thenReturn($this->block);

        $attributes = array('test' => 'test', 'tentative' => 'tentative', 'number' => 5);
        Phake::when($this->block)->getAttributes()->thenReturn($attributes);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->form)->add('field_test', 'text', array('data' => 'test', 'mapped' => false, 'label' => 'test'));
        Phake::verify($this->form)->add('field_tentative', 'text', array('data' => 'tentative', 'mapped' => false, 'label' => 'tentative'));
        Phake::verify($this->form)->add('field_number', 'text', array('data' => 5, 'mapped' => false, 'label' => 'number'));
    }

    /**
     * @param string $key
     * @param array  $value
     *
     * @dataProvider provideKeyAndData
     */
    public function testPreSetDataWithArrayAttributes($key, array $value)
    {
        Phake::when($this->event)->getData()->thenReturn($this->block);

        $attributes = array($key => $value);
        Phake::when($this->block)->getAttributes()->thenReturn($attributes);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->form)->add('field_' . $key, 'text', array(
            'label' => $key,
            'data' => json_encode($value),
            'mapped' => false
        ));
    }

    /**
     * @return array
     */
    public function provideKeyAndData()
    {
        return array(
            array('test', array('tentative' => 'tentative', 'test')),
            array('key', array('value' => 'tentative', 'test', 'other' => 4)),
        );
    }

    /**
     * @param array $sentData
     *
     * @dataProvider provideStringData
     */
    public function testPreSubmitWithNoPreviousDataAndStringDatas($sentData)
    {
        Phake::when($this->form)->getData()->thenReturn($this->block);
        Phake::when($this->block)->getAttributes()->thenReturn(array());

        $sentDataFull = array_merge(array('submit' => 'submit', 'component' => 'Sample'), $sentData);
        Phake::when($this->event)->getData()->thenReturn($sentDataFull);

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->block)->setAttributes($sentData);
    }

    /**
     * @return array
     */
    public function provideStringData()
    {
        return array(
            array(array('test' => 'test')),
            array(array('test' => 'test', 'tentative' => 'tentative')),
            array(array('test' => 'test', 'tentative' => 'tentative', 'number' => 5)),
        );
    }

    /**
     * @param array $sentData
     *
     * @dataProvider provideStringData
     */
    public function testPreSubmitWithPreviousDataAndStringDatas($sentData)
    {
        $startData = array('test' => 'oldTest');
        Phake::when($this->form)->getData()->thenReturn($this->block);
        Phake::when($this->block)->getAttributes()->thenReturn($startData);

        $sentDataFull = array_merge(array('submit' => 'submit', 'component' => 'Sample'), $sentData);
        Phake::when($this->event)->getData()->thenReturn($sentDataFull);

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->block)->setAttributes($sentData);
    }

    /**
     * @param array $sentData
     *
     * @dataProvider provideArrayData
     */
    public function testPreSubmitWithJsonEncodedData($sentData)
    {
        Phake::when($this->form)->getData()->thenReturn($this->block);
        Phake::when($this->block)->getAttributes()->thenReturn(array());

        Phake::when($this->event)->getData()->thenReturn(array('test' => json_encode($sentData)));

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->block)->setAttributes(array('test' => $sentData));
    }

    /**
     * @return array
     */
    public function provideArrayData()
    {
        return array(
            array(array()),
            array(array('tentative' => 'tentative')),
            array(array('tentative', 'test', 'autre' => 5)),
            array(array('tentative', 'test', 'autre' => array('test' => 'test'))),
        );
    }
}
