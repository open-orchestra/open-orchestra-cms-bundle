<?php

namespace OpenOrchestra\BackofficeBundle\Tests\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Phake;
use OpenOrchestra\BackofficeBundle\EventSubscriber\FieldTypeTypeSubscriber;
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
    protected $fieldOptionClass;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->fieldOptionClass = 'OpenOrchestra\ModelBundle\Document\FieldOption';
        $this->form = Phake::mock('Symfony\Component\Form\Form');
        $this->fieldType = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);
        Phake::when($this->event)->getData()->thenReturn($this->fieldType);


        $this->options = array(
            'text' => array(
                'label' => 'label text',
                'default_value' => array(
                    'label' => 'default value label',
                    'type' => 'text',
                    'options' => array(
                        'required' => 'false',
                    )
                ),
                'options' => array(
                    'max_length' => array(
                        'default_value' => 25,
                    ),
                    'required' => array(
                        'default_value' => false,
                    ),
                ),
            ),
            'hidden' => array(
                'label' => 'label hidden',
            )
        );

        $this->subscriber = new FieldTypeTypeSubscriber($this->options, $this->fieldOptionClass);
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
        $option = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldOptionInterface');
        Phake::when($option)->getKey()->thenReturn('grouping');
        $options = new ArrayCollection();
        $options->add($option);

        $type = 'text';
        Phake::when($this->fieldType)->getType()->thenReturn($type);
        Phake::when($this->fieldType)->hasOption('max_length')->thenReturn($hasMaxLength);
        Phake::when($this->fieldType)->hasOption('required')->thenReturn($hasRequired);
        Phake::when($this->fieldType)->getOptions()->thenReturn($options);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->fieldType, Phake::times($timesCalled))->addOption(Phake::anyParameters());
        Phake::verify($this->form)->add('options', 'collection', array(
            'type' => 'field_option',
            'allow_add' => false,
            'allow_delete' => false,
            'label' => false,
            'options' => array( 'label' => false ),
        ));
        Phake::verify($this->fieldType)->removeOption($option);

        $defaultValue = $this->options[$type]['default_value'];
        Phake::verify($this->form)->add('default_value', $defaultValue['type'], $defaultValue['options']);
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
        $option = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldOptionInterface');
        Phake::when($option)->getKey()->thenReturn('grouping');
        $options = new ArrayCollection();
        $options->add($option);

        $type = "text";
        Phake::when($this->fieldType)->hasOption('max_length')->thenReturn($hasMaxLength);
        Phake::when($this->fieldType)->hasOption('required')->thenReturn($hasRequired);
        Phake::when($this->fieldType)->getOptions()->thenReturn($options);

        Phake::when($this->form)->getData()->thenReturn($this->fieldType);
        Phake::when($this->event)->getData()->thenReturn(array('type' => $type));

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->fieldType, Phake::times($timesCalled))->addOption(Phake::anyParameters());
        Phake::verify($this->form)->add('options', 'collection', array(
            'type' => 'field_option',
            'allow_add' => false,
            'allow_delete' => false,
            'label' => false,
            'options' => array( 'label' => false ),
        ));
        Phake::verify($this->fieldType)->removeOption($option);

        $defaultValue = $this->options[$type]['default_value'];
        Phake::verify($this->form)->add('default_value', $defaultValue['type'], $defaultValue['options']);
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

    /**
     * Test preSetData when type has no option
     */
    public function testPreSetDataWithTypeNoOption()
    {
        Phake::when($this->fieldType)->getType()->thenReturn('hidden');

        $this->subscriber->preSetData($this->event);

        $this->verifyWithTypeNoOption();
    }

    /**
     * Test preSubmit when type has no option
     */
    public function testPreSubmitWithTypeNoOption()
    {
        Phake::when($this->form)->getData()->thenReturn($this->fieldType);
        Phake::when($this->event)->getData()->thenReturn(array('type' => 'hidden'));

        $this->subscriber->preSubmit($this->event);

        $this->verifyWithTypeNoOption();
    }

    /**
     * Verify the field type with no option
     */
    protected function verifyWithTypeNoOption()
    {
        Phake::verify($this->fieldType, Phake::times(0))->addOption(Phake::anyParameters());
        Phake::verify($this->fieldType, Phake::times(1))->clearOptions();

        Phake::verify($this->form)->add('options', 'collection', array(
            'type' => 'field_option',
            'allow_add' => false,
            'allow_delete' => false,
            'label' => false,
            'options' => array( 'label' => false ),
        ));
    }
}
