<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\EventSubscriber\FieldTypeTypeSubscriber;
use Symfony\Component\Form\FormEvents;

/**
 * Class FieldTypeTypeSubscriberTest
 */
class FieldTypeTypeSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var FieldTypeTypeSubscriber
     */
    protected $subscriber;

    protected $form;
    protected $event;
    protected $fieldTypeParameters;
    protected $fieldType;
    protected $fieldOptionClass;
    protected $fieldTypeClass;
    protected $fieldOptions;
    protected $containerChild;
    protected $optionsChild;
    protected $optionChildName = 'fakeOptionChildName';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->fieldOptionClass = 'OpenOrchestra\ModelBundle\Document\FieldOption';
        $this->fieldTypeClass = 'OpenOrchestra\ModelBundle\Document\FieldType';
        $this->form = Phake::mock('Symfony\Component\Form\Form');
        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');

        $config = Phake::mock('Symfony\Component\Form\FormConfigInterface');
        Phake::when($config)->getMethod()->thenReturn('POST');
        $parent = Phake::mock('Symfony\Component\Form\FormInterface');
        Phake::when($parent)->getConfig()->thenReturn($config);
        Phake::when($this->form)->getRoot()->thenReturn($parent);

        $this->containerChild = Phake::mock('Symfony\Component\Form\Form');
        Phake::when($this->containerChild)->has('default_value')->thenReturn(true);
        Phake::when($this->form)->get('container')->thenReturn($this->containerChild);

        $optionChild = Phake::mock('Symfony\Component\Form\Form');
        Phake::when($optionChild)->getName()->thenReturn($this->optionChildName);

        $this->optionsChild = Phake::mock('Symfony\Component\Form\Form');
        Phake::when($this->optionsChild)->all()->thenReturn(array(
            $optionChild,
        ));
        Phake::when($this->form)->get('options')->thenReturn($this->optionsChild);

        $this->fieldOptions = array(
            "text" => array("search" => 'text', 'type' => 'text'),
            "date" => array("search" => 'date', 'type' => 'date'),
        );

        $this->fieldTypeParameters = array(
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

        $this->subscriber = new FieldTypeTypeSubscriber(
            $this->fieldOptions,
            $this->fieldTypeParameters,
            $this->fieldOptionClass,
            $this->fieldTypeClass);
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
        $this->assertArrayHasKey(FormEvents::POST_SET_DATA, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(FormEvents::PRE_SUBMIT, $this->subscriber->getSubscribedEvents());
    }

    /**
     * @param bool $hasMaxLength
     * @param bool $hasRequired
     * @param int  $timesCalled
     */
    public function testPostSetData()
    {
        $defaultValue = 'fakeDefaultValue';
        $optionValue = 'fakeOptionValue';
        $expectedOptions = $this->fieldTypeParameters['text']['default_value']['options'];
        $expectedOptions['data'] = $defaultValue;

        $fieldOption = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldOptionInterface');
        Phake::when($fieldOption)->getKey()->thenReturn('text');
        Phake::when($fieldOption)->getValue()->thenReturn($optionValue);


        $fieldType = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($fieldType)->getDefaultValue()->thenReturn($defaultValue);
        Phake::when($fieldType)->getType()->thenReturn('text');
        Phake::when($fieldType)->getOptions()->thenReturn(array($fieldOption));

        Phake::when($this->event)->getData()->thenReturn($fieldType);
        Phake::when($this->event)->getForm()->thenReturn($this->form);
        Phake::when($this->form)->getData()->thenReturn($fieldType);

        $this->subscriber->postSetData($this->event);

        Phake::verify($this->containerChild)->remove('default_value');
        Phake::verify($this->optionsChild)->remove($this->optionChildName);

        Phake::verify($this->containerChild)->add(
            'default_value',
            'text',
            $expectedOptions
        );

        Phake::verify($this->optionsChild)->add('text', 'text', array("search" => 'text', 'data' => $optionValue));
    }
}
