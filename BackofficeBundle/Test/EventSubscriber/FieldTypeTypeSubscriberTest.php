<?php

namespace PHPOrchestra\BackofficeBundle\Test\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Phake;
use PHPOrchestra\BackofficeBundle\EventSubscriber\FieldTypeTypeSubscriber;
use Symfony\Component\Form\FormEvents;

/**
 * Class FieldTypeTypeSubscriberTest
 */
class FieldTypeTypeSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FieldTypeTypeSubscriber
     */
    protected $subscriber;

    protected $form;
    protected $event;
    protected $options;
    protected $fieldType;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = Phake::mock('Symfony\Component\Form\Form');
        $this->fieldType = Phake::mock('PHPOrchestra\ModelBundle\Model\FieldTypeInterface');
        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);
        Phake::when($this->event)->getData()->thenReturn($this->fieldType);


        $this->options = array(
            'text' => array(
                'label' => 'label text',
                'options' => array(
                    'max_length' => array(
                        'default_value' => 25,
                    ),
                    'required' => array(
                        'default_value' => false,
                    ),
                ),
            ),
        );

        $this->subscriber = new FieldTypeTypeSubscriber($this->options);
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
        $this->assertArrayHasKey(FormEvents::PRE_SUBMIT, $this->subscriber->getSubscribedEvents());
    }

    /**
     * @param bool $hasMaxLength
     * @param bool $hasRequired
     * @param int  $timesCalled
     *
     * @dataProvider provideMultipleCase
     */
    public function testPreSetDataWithTypeSet($hasMaxLength, $hasRequired, $timesCalled)
    {
        $option = Phake::mock('PHPOrchestra\ModelBundle\Model\FieldOptionInterface');
        Phake::when($option)->getKey()->thenReturn('grouping');
        $options = new ArrayCollection();
        $options->add($option);

        Phake::when($this->fieldType)->getType()->thenReturn('text');
        Phake::when($this->fieldType)->hasOption('max_length')->thenReturn($hasMaxLength);
        Phake::when($this->fieldType)->hasOption('required')->thenReturn($hasRequired);
        Phake::when($this->fieldType)->getOptions()->thenReturn($options);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->fieldType, Phake::times($timesCalled))->addOption(Phake::anyParameters());
        Phake::verify($this->form)->add('options', 'collection', array(
            'type' => 'field_option',
            'allow_add' => false,
            'allow_delete' => false,
            'label' => 'php_orchestra_backoffice.form.field_type.options',
        ));
        Phake::verify($this->fieldType)->removeOption($option);
    }

    /**
     * @param bool $hasMaxLength
     * @param bool $hasRequired
     * @param int  $timesCalled
     *
     * @dataProvider provideMultipleCase
     */
    public function testPreSubmitWithTypeSet($hasMaxLength, $hasRequired, $timesCalled)
    {
        $option = Phake::mock('PHPOrchestra\ModelBundle\Model\FieldOptionInterface');
        Phake::when($option)->getKey()->thenReturn('grouping');
        $options = new ArrayCollection();
        $options->add($option);

        Phake::when($this->fieldType)->hasOption('max_length')->thenReturn($hasMaxLength);
        Phake::when($this->fieldType)->hasOption('required')->thenReturn($hasRequired);
        Phake::when($this->fieldType)->getOptions()->thenReturn($options);

        Phake::when($this->form)->getData()->thenReturn($this->fieldType);
        Phake::when($this->event)->getData()->thenReturn(array('type' => 'text'));

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->fieldType, Phake::times($timesCalled))->addOption(Phake::anyParameters());
        Phake::verify($this->form)->add('options', 'collection', array(
            'type' => 'field_option',
            'allow_add' => false,
            'allow_delete' => false,
            'label' => 'php_orchestra_backoffice.form.field_type.options',
        ));
        Phake::verify($this->fieldType)->removeOption($option);
    }

    /**
     * @return array
     */
    public function provideMultipleCase()
    {
        return array(
            array(false, false, 2),
            array(false, true, 1),
            array(true, false, 1),
            array(true, true, 0),
        );
    }

    /**
     * Test when no type is choosen
     */
    public function testPreSetDataWithNoTypeSet()
    {
        Phake::when($this->fieldType)->getType()->thenReturn(null);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->fieldType, Phake::never())->addOption(Phake::anyParameters());
        Phake::verify($this->fieldType, Phake::never())->removeOption(Phake::anyParameters());
        Phake::verify($this->form, Phake::never())->add(Phake::anyParameters());
    }
}
