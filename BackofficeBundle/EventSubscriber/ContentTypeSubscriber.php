<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\Backoffice\ValueTransformer\ValueTransformerManager;
use OpenOrchestra\ModelInterface\Model\ContentAttributeInterface;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ContentTypeSubscriber
 */
class ContentTypeSubscriber extends AbstractModulableTypeSubscriber
{
    protected $translationChoiceManager;
    protected $contentTypeRepository;
    protected $contentAttributeClass;
    protected $fieldTypesConfiguration;
    protected $valueTransformerManager;
    protected $translator;

    /**
     * @param ContentTypeRepositoryInterface $contentTypeRepository
     * @param string                         $contentAttributeClass
     * @param TranslationChoiceManager       $translationChoiceManager
     * @param array                          $fieldTypesConfiguration
     * @param ValueTransformerManager        $valueTransformerManager
     * @param TranslatorInterface            $translator
     */
    public function __construct(
        ContentTypeRepositoryInterface $contentTypeRepository,
        $contentAttributeClass,
        TranslationChoiceManager $translationChoiceManager,
        $fieldTypesConfiguration,
        ValueTransformerManager $valueTransformerManager,
        TranslatorInterface $translator
    )
    {
        $this->contentTypeRepository = $contentTypeRepository;
        $this->contentAttributeClass = $contentAttributeClass;
        $this->translationChoiceManager = $translationChoiceManager;
        $this->fieldTypesConfiguration = $fieldTypesConfiguration;
        $this->valueTransformerManager = $valueTransformerManager;
        $this->translator = $translator;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array_merge(
            parent::getSubscribedEvents(),
            array(FormEvents::POST_SET_DATA => 'postSetData')
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $contentType = $this->contentTypeRepository->findOneByContentTypeIdInLastVersion($data->getContentType());

        if ($contentType instanceof ContentTypeInterface) {
            $data->setContentTypeVersion($contentType->getVersion());
            $this->addContentTypeFieldsToForm($contentType->getFields(), $event->getForm(), $data);
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
        $form = $event->getForm();
        $content = $form->getData();
        $contentType = $this->contentTypeRepository->findOneByContentTypeIdInLastVersion($content->getContentType());

        if ($contentType instanceof ContentTypeInterface) {
            $data = $event->getData();
            $content->setContentTypeVersion($contentType->getVersion());
            foreach ($contentType->getFields() as $contentTypeField) {
                $contentTypeFieldId = $contentTypeField->getFieldId();
                $data[$contentTypeFieldId] = isset($data[$contentTypeFieldId]) ? $data[$contentTypeFieldId] : null;
                $value = $this->transformData($data[$contentTypeFieldId], $form->get($contentTypeFieldId));

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
     * @param ContentInterface          $data
     */
    protected function addContentTypeFieldsToForm($contentTypeFields, FormInterface $form, ContentInterface $data)
    {
        /** @var FieldTypeInterface $contentTypeField */
        foreach ($contentTypeFields as $contentTypeField) {

            if (isset($this->fieldTypesConfiguration[$contentTypeField->getType()])) {
                $this->addFieldToForm($contentTypeField, $form);
            }
        }
    }

    /**
     * Add $contentTypeField to $form with value $fieldValue
     * 
     * @param FieldTypeInterface $contentTypeField
     * @param FormInterface      $form
     */
    protected function addFieldToForm(FieldTypeInterface $contentTypeField, FormInterface $form)
    {
        $fieldTypeConfiguration = $this->fieldTypesConfiguration[$contentTypeField->getType()];

        $fieldParameters = array_merge(
            array(
                'label' => $this->translationChoiceManager->choose($contentTypeField->getLabels()),
                'mapped' => false,
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
        $configuratedOptions = $this->fieldTypesConfiguration[$contentTypeField->getType()]['options'];
        $options = array();

        foreach ($configuratedOptions as $optionName => $optionConfiguration) {
            $options[$optionName] = (isset($contentTypeOptions[$optionName])) ? $contentTypeOptions[$optionName] : $optionConfiguration['default_value'];
        }

        return $options;
    }
}
