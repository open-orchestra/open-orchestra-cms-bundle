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
    protected $contentAttributClass;
    protected $contentTypeId;
    protected $fieldCollection;
    protected $contentAttribute;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->contentAttributClass = 'PHPOrchestra\ModelBundle\Document\ContentAttribute';

        $this->contentTypeId = 'contentTypeId';
        $this->form = Phake::mock('Symfony\Component\Form\FormBuilder');
        $this->contentAttribute = Phake::mock('PHPOrchestra\ModelBundle\Model\ContentAttributeInterface');
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
        Phake::when($this->repository)->find(Phake::anyParameters())->thenReturn($this->contentType);

        $this->subscriber = new ContentTypeSubscriber($this->repository, $this->contentAttributClass);
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
        $this->assertArrayHasKey(FormEvents::PRE_SUBMIT, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test preSetData
     */
    public function testPreSetDataWithNoDatas()
    {
        Phake::when($this->event)->getData()->thenReturn($this->content);
        $this->fieldCollection->add($this->fieldType1);
        $this->fieldCollection->add($this->fieldType1);

        $fieldId = 'title';
        $label = 'Title';
        $defaultValue = '';
        $type = 'text';
        $options = array(
            'max_length' => 25,
            'required' => true
        );

        Phake::when($this->fieldType1)->getFieldId()->thenReturn($fieldId);
        Phake::when($this->fieldType1)->getLabel()->thenReturn($label);
        Phake::when($this->fieldType1)->getDefaultValue()->thenReturn($defaultValue);
        Phake::when($this->fieldType1)->getType()->thenReturn($type);
        Phake::when($this->fieldType1)->getOptions()->thenReturn($options);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->repository)->findOneByContentTypeId($this->contentTypeId);
        Phake::verify($this->form, Phake::times(2))->add($fieldId, $type, array_merge(
            array(
                'data' => $defaultValue,
                'label' => $label,
                'mapped' => false
            ),
            $options
        ));
    }

    /**
     * Test with no content type
     */
    public function testPreSetDataWithNoContentTypeFound()
    {
        Phake::when($this->repository)->findOneByContentTypeId(Phake::anyParameters())->thenReturn(null);
        Phake::when($this->event)->getData()->thenReturn($this->content);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->repository)->findOneByContentTypeId($this->contentTypeId);
        Phake::verify($this->form, Phake::never())->add(Phake::anyParameters());
    }

    /**
     * Test with existing data
     */
    public function testPreSetDataWithExistingDatas()
    {
        Phake::when($this->event)->getData()->thenReturn($this->content);
        $this->fieldCollection->add($this->fieldType1);
        $this->fieldCollection->add($this->fieldType1);

        $fieldId = 'title';
        $label = 'Title';
        $defaultValue = '';
        $type = 'text';
        $options = array(
            'max_length' => 25,
            'required' => true
        );
        Phake::when($this->fieldType1)->getFieldId()->thenReturn($fieldId);
        Phake::when($this->fieldType1)->getLabel()->thenReturn($label);
        Phake::when($this->fieldType1)->getDefaultValue()->thenReturn($defaultValue);
        Phake::when($this->fieldType1)->getType()->thenReturn($type);
        Phake::when($this->fieldType1)->getOptions()->thenReturn($options);

        $realValue = 'realValue';
        Phake::when($this->contentAttribute)->getValue()->thenReturn($realValue);
        Phake::when($this->content)->getAttributeByName($fieldId)->thenReturn($this->contentAttribute);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->repository)->findOneByContentTypeId($this->contentTypeId);
        Phake::when($this->content)->getAttributeByName($fieldId);
        Phake::verify($this->form, Phake::times(2))->add($fieldId, $type, array_merge(
            array(
                'data' => $realValue,
                'label' => $label,
                'mapped' => false
            ),
            $options
        ));
    }

    /**
     * Test submit
     */
    public function testSubmitWithExistingData()
    {
        $name = 'name';
        $status = 'status';
        $language = 'fr';
        $realContentTypeId = 'thisIsAnId';
        $title = 'Content title';
        $data = array(
            'name' => $name,
            'status' => $status,
            'language' => $language,
            'contentType' => $realContentTypeId,
            'title' => $title,
        );
        Phake::when($this->event)->getData()->thenReturn($data);

        Phake::when($this->form)->getData()->thenReturn($this->content);

        $this->fieldCollection->add($this->fieldType1);
        $fieldId = 'title';
        $label = 'Title';
        $defaultValue = '';
        $type = 'text';
        $options = array(
            'max_length' => 25,
            'required' => true
        );
        Phake::when($this->fieldType1)->getFieldId()->thenReturn($fieldId);
        Phake::when($this->fieldType1)->getLabel()->thenReturn($label);
        Phake::when($this->fieldType1)->getDefaultValue()->thenReturn($defaultValue);
        Phake::when($this->fieldType1)->getType()->thenReturn($type);
        Phake::when($this->fieldType1)->getOptions()->thenReturn($options);

        Phake::when($this->content)->getAttributeByName(Phake::anyParameters())->thenReturn($this->contentAttribute);

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->form)->getData();
        Phake::verify($this->repository)->find($realContentTypeId);
        Phake::verify($this->content)->getAttributeByName($fieldId);
        Phake::verify($this->contentAttribute)->setValue($title);
    }

    /**
     * Test submit
     */
    public function testSubmitWithNoExistingData()
    {
        $name = 'name';
        $status = 'status';
        $language = 'fr';
        $realContentTypeId = 'thisIsAnId';
        $title = 'Content title';
        $data = array(
            'name' => $name,
            'status' => $status,
            'language' => $language,
            'contentType' => $realContentTypeId,
            'title' => $title,
        );
        Phake::when($this->event)->getData()->thenReturn($data);

        Phake::when($this->form)->getData()->thenReturn($this->content);

        $this->fieldCollection->add($this->fieldType1);
        $fieldId = 'title';
        $defaultValue = '';
        Phake::when($this->fieldType1)->getFieldId()->thenReturn($fieldId);
        Phake::when($this->fieldType1)->getDefaultValue()->thenReturn($defaultValue);

        Phake::when($this->content)->getAttributeByName(Phake::anyParameters())->thenReturn(null);

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->form)->getData();
        Phake::verify($this->repository)->find($realContentTypeId);
        Phake::verify($this->content)->getAttributeByName($fieldId);
        Phake::verify($this->content)->addAttribute(Phake::anyParameters());
    }
}
