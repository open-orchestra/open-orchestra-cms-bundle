<?php

namespace OpenOrchestra\BackofficeBundle\Tests\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Phake;
use OpenOrchestra\BackofficeBundle\EventSubscriber\ContentTypeSubscriber;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

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
    protected $collection;
    protected $repository;
    protected $fieldType1;
    protected $fieldType2;
    protected $fieldType3;
    protected $formConfig;
    protected $contentType;
    protected $contentTypeId;
    protected $fieldCollection;
    protected $contentAttribute;
    protected $contentAttributClass;
    protected $contentTypeVersion = 1;
    protected $translationChoiceManager;
    protected $fieldTypesConfiguration;
    protected $constraintsNotBlank;
    protected $valueTransformerManager;
    protected $eventDispatcher;
    protected $contentAttributeClass;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->fieldTypesConfiguration = array(
            'text' => 
                array(
                    'type' => 'text',
                    'options' => array(
                        'max_length' => array('default_value' => 12),
                        'required' => array('default_value' => 'false'),
                        'phake_option' => array('default_value' => 'phake_value')
                    )
                )
        );

        $this->collection = Phake::mock('Doctrine\Common\Collections\Collection');

        $this->contentAttributClass = 'OpenOrchestra\ModelBundle\Document\ContentAttribute';

        $this->formConfig = Phake::mock('Symfony\Component\Form\FormConfigInterface');
        Phake::when($this->formConfig)->getModelTransformers()->thenReturn(array());
        Phake::when($this->formConfig)->getViewTransformers()->thenReturn(array());
        $this->form = Phake::mock('Symfony\Component\Form\Form');
        Phake::when($this->form)->get(Phake::anyParameters())->thenReturn($this->form);
        Phake::when($this->form)->getConfig()->thenReturn($this->formConfig);

        $this->contentTypeId = 'contentTypeId';
        $this->contentAttribute = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentAttributeInterface');
        Phake::when($this->contentAttribute)->getType()->thenReturn("bar");
        $this->content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($this->content)->getContentType()->thenReturn($this->contentTypeId);
        Phake::when($this->content)->getAttributeByName(Phake::anyParameters())->thenReturn($this->contentAttribute);

        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);
        Phake::when($this->event)->getData()->thenReturn($this->content);

        $this->fieldType1 = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($this->fieldType1)->getLabels()->thenReturn($this->collection);
        $this->fieldType2 = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($this->fieldType2)->getLabels()->thenReturn($this->collection);
        $this->fieldType3 = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($this->fieldType3)->getLabels()->thenReturn($this->collection);
        $this->fieldCollection = new ArrayCollection();
        $this->contentType = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($this->contentType)->getFields()->thenReturn($this->fieldCollection);
        Phake::when($this->contentType)->getVersion()->thenReturn($this->contentTypeVersion);

        $this->repository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface');
        Phake::when($this->repository)->findOneByContentTypeIdInLastVersion(Phake::anyParameters())->thenReturn($this->contentType);
        Phake::when($this->repository)->find(Phake::anyParameters())->thenReturn($this->contentType);

        $this->translationChoiceManager = Phake::mock('OpenOrchestra\Backoffice\Manager\TranslationChoiceManager');
        $this->constraintsNotBlank =  new NotBlank();

        $this->contentAttributeClass = 'OpenOrchestra\ModelBundle\Document\ContentAttribute';

        $this->valueTransformerManager = Phake::mock('OpenOrchestra\Backoffice\ValueTransformer\ValueTransformerManager');
        Phake::when($this->valueTransformerManager)->transform(Phake::anyParameters())->thenReturn('foo');

        $translator = Phake::mock('Symfony\Component\Translation\Translator');
        $this->subscriber = new ContentTypeSubscriber(
            $this->repository,
            $this->contentAttributClass,
            $this->translationChoiceManager,
            $this->fieldTypesConfiguration,
            $this->valueTransformerManager,
            $translator
        );

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
        $this->assertArrayHasKey(FormEvents::POST_SET_DATA, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(FormEvents::PRE_SUBMIT, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test preSetData
     */
    public function testPreSetDataWithNoDatas()
    {
        $this->fieldCollection->add($this->fieldType1);
        $this->fieldCollection->add($this->fieldType1);

        $fieldId = 'title';
        $label = 'Title';
        $type = 'text';
        $options = array(
            'max_length' => 25,
            'required' => true,
            'oldOption' => 'oldValue'
        );
        $expectedOptions = array(
            'label' => $label,
            'mapped' => false,
            'max_length' => 25,
            'required' => true,
            'phake_option' => 'phake_value',
            'constraints' => $this->constraintsNotBlank
        );

        Phake::when($this->fieldType1)->getFieldId()->thenReturn($fieldId);
        Phake::when($this->translationChoiceManager)->choose(Phake::anyParameters())->thenReturn($label);
        Phake::when($this->fieldType1)->getType()->thenReturn($type);
        Phake::when($this->fieldType1)->getFormOptions()->thenReturn($options);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->repository)->findOneByContentTypeIdInLastVersion($this->contentTypeId);
        Phake::verify($this->content)->setContentTypeVersion($this->contentTypeVersion);
        Phake::verify($this->form, Phake::times(2))->add($fieldId, $type, $expectedOptions);
    }

    /**
     * Test with no content type
     */
    public function testPreSetDataWithNoContentTypeFound()
    {
        Phake::when($this->repository)->findOneByContentTypeIdInLastVersion(Phake::anyParameters())->thenReturn(null);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->repository)->findOneByContentTypeIdInLastVersion($this->contentTypeId);
        Phake::verify($this->form, Phake::never())->add(Phake::anyParameters());
    }

    /**
     * Test with existing data
     */
    public function testPreSetDataWithExistingDatas()
    {
        $this->fieldCollection->add($this->fieldType1);
        $this->fieldCollection->add($this->fieldType1);

        $fieldId = 'title';
        $label = 'Title';
        $realValue = 'realValue';
        $type = 'text';
        $options = array(
            'max_length' => 25,
            'required' => true,
            'oldOption' => 'oldValue'
        );
        $expectedOptions = array(
            'label' => $label,
            'mapped' => false,
            'max_length' => 25,
            'required' => true,
            'phake_option' => 'phake_value',
            'constraints' => $this->constraintsNotBlank
        );

        Phake::when($this->fieldType1)->getFieldId()->thenReturn($fieldId);
        Phake::when($this->translationChoiceManager)->choose(Phake::anyParameters())->thenReturn($label);
        Phake::when($this->fieldType1)->getType()->thenReturn($type);
        Phake::when($this->fieldType1)->getFormOptions()->thenReturn($options);

        Phake::when($this->contentAttribute)->getValue()->thenReturn($realValue);
        Phake::when($this->content)->getAttributeByName($fieldId)->thenReturn($this->contentAttribute);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->repository)->findOneByContentTypeIdInLastVersion($this->contentTypeId);
        Phake::verify($this->content)->setContentTypeVersion($this->contentTypeVersion);
        Phake::when($this->content)->getAttributeByName($fieldId);
        Phake::verify($this->form, Phake::times(2))->add($fieldId, $type, $expectedOptions);
    }

    /**
     * Test submit
     */
    public function testSubmitWithExistingData()
    {
        $name = 'name';
        $status = 'status';
        $language = 'fr';
        $realContentTypeId = 'contentTypeId';
        $realValueArray = array('value1', 'value2');
        $data = array(
            'name' => $name,
            'status' => $status,
            'language' => $language,
            'contentType' => $realContentTypeId,
            'title' => $realValueArray,
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
        Phake::when($this->translationChoiceManager)->choose(Phake::anyParameters())->thenReturn($label);
        Phake::when($this->fieldType1)->getDefaultValue()->thenReturn($defaultValue);
        Phake::when($this->fieldType1)->getType()->thenReturn($type);
        Phake::when($this->fieldType1)->getFormOptions()->thenReturn($options);

        Phake::when($this->content)->getAttributeByName($fieldId)->thenReturn($this->contentAttribute);

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->form)->getData();
        Phake::verify($this->repository)->findOneByContentTypeIdInLastVersion($realContentTypeId);
        Phake::verify($this->content)->getAttributeByName($fieldId);
        Phake::verify($this->contentAttribute)->setValue($realValueArray);
        Phake::verify($this->contentAttribute)->setType($type);
        Phake::verify($this->valueTransformerManager)->transform('bar', $realValueArray);
        Phake::verify($this->contentAttribute)->setStringValue('foo');
    }

    /**
     * Test submit
     */
    public function testSubmitWithNoExistingData()
    {
        $name = 'name';
        $status = 'status';
        $language = 'fr';
        $realContentTypeId = 'contentTypeId';
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

        Phake::when($this->content)->getAttributeByName($fieldId)->thenReturn(null);

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->form)->getData();
        Phake::verify($this->repository)->findOneByContentTypeIdInLastVersion($realContentTypeId);
        Phake::verify($this->content)->getAttributeByName($fieldId);
        Phake::verify($this->content)->addAttribute(Phake::anyParameters());
        Phake::verify($this->valueTransformerManager)->transform(null, $title);
    }
}
