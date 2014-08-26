<?php

namespace PHPOrchestra\BackofficeBundle\Test\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Phake;
use PHPOrchestra\BackofficeBundle\EventSubscriber\ContentTypeSubscriber;
use Symfony\Component\Form\FormEvents;

/**
 * Class ContentTypeSubscriberTest
 */
class ContentTypeSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentTypeSubscriber
     */
    protected $subscriber;

    protected $form;
    protected $event;
    protected $content;
    protected $repository;
    protected $fieldType1;
    protected $fieldType2;
    protected $fieldType3;
    protected $contentType;
    protected $contentTypeId;
    protected $fieldCollection;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->contentTypeId = 'contentTypeId';
        $this->form = Phake::mock('Symfony\Component\Form\FormBuilder');
        $this->content = Phake::mock('PHPOrchestra\ModelBundle\Model\ContentInterface');
        Phake::when($this->content)->getContentType()->thenReturn($this->contentTypeId);

        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);

        $this->fieldType1 = Phake::mock('PHPOrchestra\ModelBundle\Model\FieldTypeInterface');
        $this->fieldType2 = Phake::mock('PHPOrchestra\ModelBundle\Model\FieldTypeInterface');
        $this->fieldType3 = Phake::mock('PHPOrchestra\ModelBundle\Model\FieldTypeInterface');
        $this->fieldCollection = new ArrayCollection();
        $this->contentType = Phake::mock('PHPOrchestra\ModelBundle\Model\ContentTypeInterface');
        Phake::when($this->contentType)->getFields()->thenReturn($this->fieldCollection);

        $this->repository = Phake::mock('PHPOrchestra\ModelBundle\Repository\ContentTypeRepository');
        Phake::when($this->repository)->findOneByContentTypeId(Phake::anyParameters())->thenReturn($this->contentType);

        $this->subscriber = new ContentTypeSubscriber($this->repository);
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
        $this->assertArrayHasKey(FormEvents::PRE_SET_DATA, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test preSetData
     */
    public function testPreSetData()
    {
        Phake::when($this->event)->getData()->thenReturn($this->content);
        $this->fieldCollection->add($this->fieldType1);
        $this->fieldCollection->add($this->fieldType1);

        $fieldId = 'title';
        $label = 'Title';
        $defaultValue = '';
        $symfonyType = 'text';
        $options = array(
            'max_length' => 25,
            'required' => true
        );

        Phake::when($this->fieldType1)->getFieldId()->thenReturn($fieldId);
        Phake::when($this->fieldType1)->getLabel()->thenReturn($label);
        Phake::when($this->fieldType1)->getDefaultValue()->thenReturn($defaultValue);
        Phake::when($this->fieldType1)->getSymfonyType()->thenReturn($symfonyType);
        Phake::when($this->fieldType1)->getOptions()->thenReturn($options);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->repository)->findOneByContentTypeId($this->contentTypeId);
        Phake::verify($this->form, Phake::times(2))->add($fieldId, $symfonyType, array_merge(
            array(
                'data' => $defaultValue,
                'label' => $label,
                'mapped' => false
            ),
            $options
        ));
    }
}
