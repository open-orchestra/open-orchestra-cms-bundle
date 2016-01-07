<?php

namespace OpenOrchestra\BackofficeBundle\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\BackofficeBundle\EventSubscriber\FieldOptionTypeSubscriber;
use Symfony\Component\Form\FormEvents;

/**
 * Class FieldOptionTypeSubscriberTest
 */
class FieldOptionTypeSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var FieldOptionTypeSubscriber
     */
    protected $subscriber;

    protected $form;
    protected $event;
    protected $options;
    protected $fieldOption;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = Phake::mock('Symfony\Component\Form\Form');
        $this->fieldOption = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldOptionInterface');
        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);
        Phake::when($this->event)->getData()->thenReturn($this->fieldOption);

        $this->options = array(
            'max_length' => array(
                'type' => 'integer',
                'label' => 'label integer',
                'required' => false,
            ),
            'required' => array(
                'type' => 'checkbox',
                'label' => 'checkbox',
                'required' => false,
            ),
        );

        $this->subscriber = new FieldOptionTypeSubscriber($this->options);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * Test assert subscribed event
     */
    public function testEventSubscribed()
    {
        $this->assertArrayHasKey(FormEvents::PRE_SET_DATA, $this->subscriber->getSubscribedEvents());
    }

    /**
     * @param string $key
     *
     * @dataProvider provideKey
     */
    public function testPreSetData($key)
    {
        Phake::when($this->fieldOption)->getKey()->thenReturn($key);

        $this->subscriber->preSetData($this->event);
        Phake::verify($this->form)->add('value', $this->options[$key]['type'], array(
            'label' => $this->options[$key]['label'],
            'required' => $this->options[$key]['required'],
        ));
    }

    /**
     * @return array
     */
    public function provideKey()
    {
        return array(
            array('max_length'),
            array('required'),
        );
    }
}
