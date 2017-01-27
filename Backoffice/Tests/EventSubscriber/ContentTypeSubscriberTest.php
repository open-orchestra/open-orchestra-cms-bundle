<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\EventSubscriber\ContentTypeSubscriber;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ContentTypeSubscriberTest
 */
class ContentTypeSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var ContentTypeSubscriber
     */
    protected $subscriber;

    protected $form;
    protected $subForm;
    protected $event;
    protected $content;
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
    protected $multiLanguagesChoiceManager;
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

        $this->contentAttributClass = 'OpenOrchestra\ModelBundle\Document\ContentAttribute';

        $this->formConfig = Phake::mock('Symfony\Component\Form\FormConfigInterface');
        Phake::when($this->formConfig)->getModelTransformers()->thenReturn(array());
        Phake::when($this->formConfig)->getViewTransformers()->thenReturn(array());
        $this->form = Phake::mock('Symfony\Component\Form\Form');
        $this->subForm = Phake::mock('Symfony\Component\Form\Form');
        Phake::when($this->form)->get(Phake::anyParameters())->thenReturn($this->subForm);
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
        Phake::when($this->fieldType1)->getLabels()->thenReturn(array());
        $this->fieldType2 = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($this->fieldType2)->getLabels()->thenReturn(array());
        $this->fieldType3 = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($this->fieldType3)->getLabels()->thenReturn(array());
        $this->fieldCollection = new ArrayCollection();
        $this->contentType = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($this->contentType)->getFields()->thenReturn($this->fieldCollection);

        $this->repository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface');
        Phake::when($this->repository)->findOneByContentTypeIdInLastVersion(Phake::anyParameters())->thenReturn($this->contentType);
        Phake::when($this->repository)->find(Phake::anyParameters())->thenReturn($this->contentType);

        $this->multiLanguagesChoiceManager = Phake::mock('OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface');
        $this->constraintsNotBlank =  new NotBlank();

        $this->contentAttributeClass = 'OpenOrchestra\ModelBundle\Document\ContentAttribute';

        $this->valueTransformerManager = Phake::mock('OpenOrchestra\Backoffice\ValueTransformer\ValueTransformerManager');
        Phake::when($this->valueTransformerManager)->transform(Phake::anyParameters())->thenReturn('foo');

        $translator = Phake::mock('Symfony\Component\Translation\Translator');
        $this->subscriber = new ContentTypeSubscriber(
            $this->repository,
            $this->contentAttributClass,
            $this->multiLanguagesChoiceManager,
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
        $this->assertArrayHasKey(FormEvents::POST_SET_DATA, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(FormEvents::POST_SUBMIT, $this->subscriber->getSubscribedEvents());
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
        Phake::when($this->subForm)->getData()->thenReturn($realValueArray);
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
        Phake::when($this->multiLanguagesChoiceManager)->choose(Phake::anyParameters())->thenReturn($label);
        Phake::when($this->fieldType1)->getDefaultValue()->thenReturn($defaultValue);
        Phake::when($this->fieldType1)->getType()->thenReturn($type);
        Phake::when($this->fieldType1)->getFormOptions()->thenReturn($options);

        Phake::when($this->content)->getAttributeByName($fieldId)->thenReturn($this->contentAttribute);

        $this->subscriber->postSubmit($this->event);

        Phake::verify($this->form)->getData();
        Phake::verify($this->subForm)->getData();
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
        Phake::when($this->subForm)->getData()->thenReturn($title);

        $this->fieldCollection->add($this->fieldType1);
        $fieldId = 'title';
        $defaultValue = '';
        Phake::when($this->fieldType1)->getFieldId()->thenReturn($fieldId);
        Phake::when($this->fieldType1)->getDefaultValue()->thenReturn($defaultValue);

        Phake::when($this->content)->getAttributeByName($fieldId)->thenReturn(null);

        $this->subscriber->postSubmit($this->event);

        Phake::verify($this->form)->getData();
        Phake::verify($this->subForm)->getData();
        Phake::verify($this->repository)->findOneByContentTypeIdInLastVersion($realContentTypeId);
        Phake::verify($this->content)->getAttributeByName($fieldId);
        Phake::verify($this->content)->addAttribute(Phake::anyParameters());
        Phake::verify($this->valueTransformerManager)->transform(null, $title);
    }
}
