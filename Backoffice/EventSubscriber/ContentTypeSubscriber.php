<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use OpenOrchestra\Backoffice\ValueTransformer\ValueTransformerManager;
use OpenOrchestra\Backoffice\Exception\StatusChangeNotGrantedException;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use OpenOrchestra\ModelInterface\Model\ContentAttributeInterface;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use OpenOrchestra\ModelInterface\Event\StatusableEvent;
use OpenOrchestra\ModelInterface\StatusEvents;

/**
 * Class ContentTypeSubscriber
 */
class ContentTypeSubscriber implements EventSubscriberInterface
{
    protected $multiLanguagesChoiceManager;
    protected $contentTypeRepository;
    protected $contentAttributeClass;
    protected $fieldTypesConfiguration;
    protected $valueTransformerManager;
    protected $translator;

    /**
     * @param ContentTypeRepositoryInterface       $contentTypeRepository
     * @param StatusRepositoryInterface            $statusRepository
     * @param string                               $contentAttributeClass
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     * @param array                                $fieldTypesConfiguration
     * @param ValueTransformerManager              $valueTransformerManager
     * @param TranslatorInterface                  $translator
     */
    public function __construct(
        ContentTypeRepositoryInterface $contentTypeRepository,
        StatusRepositoryInterface $statusRepository,
        $contentAttributeClass,
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager,
        $fieldTypesConfiguration,
        ValueTransformerManager $valueTransformerManager,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->contentTypeRepository = $contentTypeRepository;
        $this->statusRepository = $statusRepository;
        $this->contentAttributeClass = $contentAttributeClass;
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
        $this->fieldTypesConfiguration = $fieldTypesConfiguration;
        $this->valueTransformerManager = $valueTransformerManager;
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
            FormEvents::POST_SUBMIT => 'postSubmit',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $content = $event->getData();
        $contentType = $this->contentTypeRepository->findOneByContentTypeIdInLastVersion($content->getContentType());
        if ($contentType instanceof ContentTypeInterface) {
            $this->addContentTypeFieldsToForm($contentType->getFields(), $form, $content->getStatus() ? $content->getStatus()->isBlockedEdition() : false);
        }
    }

    /**
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $contentType = $this->contentTypeRepository->findOneByContentTypeIdInLastVersion($data->getContentType());
        if ($contentType instanceof ContentTypeInterface) {
            foreach ($contentType->getFields() as $contentTypeField) {
                $contentTypeFieldId = $contentTypeField->getFieldId();
                $dataAttribute = $data->getAttributeByName($contentTypeFieldId);
                $fieldValue = ($dataAttribute) ? $dataAttribute->getValue() : $contentTypeField->getDefaultValue();
                try {
                    $form->get($contentTypeFieldId)->setData($fieldValue);
                } catch (TransformationFailedException $e) {
                    $message = $this->translator->trans("open_orchestra_backoffice.form.content.transformation_error");
                    $error = new FormError($message);
                    $form->get($contentTypeFieldId)->addError($error);
                }
            }
        }
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $content = $event->getForm()->getData();
        $data = $event->getData();
        $statusId = !array_key_exists('status', $data) ? false : $data['status'];

        if ($content instanceof ContentInterface && $statusId && $content->getStatus()->getId() != $statusId) {
            $toStatus = $this->statusRepository->find($statusId);
            $event = new StatusableEvent($content, $toStatus);
            try {
                $this->eventDispatcher->dispatch(StatusEvents::STATUS_CHANGE, $event);
            } catch (StatusChangeNotGrantedException $e) {
                throw new StatusChangeNotGrantedException();
            }
        }
    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $content = $form->getData();
        $contentType = $this->contentTypeRepository->findOneByContentTypeIdInLastVersion($content->getContentType());

        if ($contentType instanceof ContentTypeInterface) {
            foreach ($contentType->getFields() as $contentTypeField) {
                $contentTypeFieldId = $contentTypeField->getFieldId();
                $value = $form->get($contentTypeFieldId)->getData();
                $attribute = $content->getAttributeByName($contentTypeFieldId);
                if (is_null($attribute)) {
                    /** @var ContentAttributeInterface $attribute */
                    $attribute = new $this->contentAttributeClass();
                    $attribute->setName($contentTypeFieldId);
                    $content->addAttribute($attribute);
                }

                $attribute->setValue($value);
                $attribute->setType($contentTypeField->getType());
                $attribute->setStringValue($this->valueTransformerManager->transform($attribute->getType(), $value));
            }
        }
    }

    /**
     * Add $contentTypeFields to $form with values in $data if content type is still valid
     *
     * @param array<FieldTypeInterface> $contentTypeFields
     * @param FormInterface             $form
     * @param boolean                   $blockedEdition
     */
    protected function addContentTypeFieldsToForm($contentTypeFields, FormInterface $form, $blockedEdition)
    {
        /** @var FieldTypeInterface $contentTypeField */
        foreach ($contentTypeFields as $contentTypeField) {

            if (isset($this->fieldTypesConfiguration[$contentTypeField->getType()])) {
                $this->addFieldToForm($contentTypeField, $form, $blockedEdition);
            }
        }
    }

    /**
     * Add $contentTypeField to $form with value $fieldValue
     *
     * @param FieldTypeInterface $contentTypeField
     * @param FormInterface      $form
     * @param boolean            $blockedEdition
     */
    protected function addFieldToForm(FieldTypeInterface $contentTypeField, FormInterface $form, $blockedEdition)
    {
        $fieldTypeConfiguration = $this->fieldTypesConfiguration[$contentTypeField->getType()];

        $fieldParameters = array_merge(
            array(
                'label' => $this->multiLanguagesChoiceManager->choose($contentTypeField->getLabels()),
                'mapped' => false,
                'disabled' => $blockedEdition,
                'group_id' => 'data',
                'sub_group_id' => 'data',
            ),
            $this->getFieldOptions($contentTypeField)
        );

        if (isset($fieldParameters['required']) && $fieldParameters['required'] === true) {
            $fieldParameters['constraints'] = new NotBlank();
        }
        $form->add(
            $contentTypeField->getFieldId(),
            $fieldTypeConfiguration['type'],
            $fieldParameters
        );
    }

    /**
     * Get $contentTypeField options from conf and complete it with $contentTypeField setted values
     *
     * @param FieldTypeInterface $contentTypeField
     *
     * @return array
     */
    protected function getFieldOptions(FieldTypeInterface $contentTypeField)
    {
        $contentTypeOptions = $contentTypeField->getFormOptions();
        $options = array();
        $field = $this->fieldTypesConfiguration[$contentTypeField->getType()];
        if (isset($field['options'])) {
            $configuratedOptions = $field['options'];
            foreach ($configuratedOptions as $optionName => $optionConfiguration) {
                $options[$optionName] = (isset($contentTypeOptions[$optionName])) ? $contentTypeOptions[$optionName] : $optionConfiguration['default_value'];
            }
        }
        return $options;
    }
}
