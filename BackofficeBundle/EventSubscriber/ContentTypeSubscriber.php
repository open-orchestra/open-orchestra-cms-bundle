<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;

/**
 * Class ContentTypeSubscriber
 */
class ContentTypeSubscriber extends AbstractBlockContentTypeSubscriber
{
    protected $translationChoiceManager;
    protected $contentTypeRepository;
    protected $contentAttributClass;
    protected $fieldTypesConfiguration;

    /**
     * @param ContentTypeRepositoryInterface $contentTypeRepository
     * @param string                         $contentAttributClass
     * @param TranslationChoiceManager       $translationChoiceManager
     * @param string                         $fieldTypesConfiguration
     */
    public function __construct(
        ContentTypeRepositoryInterface $contentTypeRepository,
        $contentAttributClass,
        TranslationChoiceManager $translationChoiceManager,
        $fieldTypesConfiguration
    )
    {
        $this->contentTypeRepository = $contentTypeRepository;
        $this->contentAttributClass = $contentAttributClass;
        $this->translationChoiceManager = $translationChoiceManager;
        $this->fieldTypesConfiguration = $fieldTypesConfiguration;
    }

    /**
     * @param FormEvent $eventFieldType
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $contentType = $this->contentTypeRepository->findOneByContentTypeIdInLastVersion($data->getContentType());

        if (is_object($contentType)) {
            $this->addContentTypeFieldsToForm($contentType, $event->getForm(), $data);
        }
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $content = $form->getData();
        $data = $event->getData();
        $contentType = $this->contentTypeRepository->findOneByContentTypeIdInLastVersion($content->getContentType());

        if (is_object($contentType)) {
            $content->setContentTypeVersion($contentType->getVersion());
            foreach ($contentType->getFields() as $field) {
                $fieldId = $field->getFieldId();
                if ($attribute = $content->getAttributeByName($fieldId)) {
                    $attribute->setValue($this->transformData($data[$fieldId], $form->get($fieldId)));
                } elseif (is_null($attribute)) {
                    $contentAttributClass = $this->contentAttributClass;
                    $attribute = new $contentAttributClass;
                    $attribute->setName($fieldId);
                    $attribute->setValue($this->transformData($data[$fieldId], $form->get($fieldId)));
                    $content->addAttribute($attribute);
                }
            }
        }
    }

    /**
     * Add $contentType fields to $form with $data if content type is still valid
     * 
     * @param ContentTypeInterface $contentType
     * @param FormInterface        $form
     * @param ContentInterface     $data
     */
    protected function addContentTypeFieldsToForm(ContentTypeInterface $contentType, FormInterface $form, ContentInterface $data)
    {
        $data->setContentTypeVersion($contentType->getVersion());

        /** @var FieldTypeInterface $contentTypeField */
        foreach ($contentType->getFields() as $contentTypeField) {
            if (isset($this->fieldTypesConfiguration[$contentTypeField->getType()])) {
                $savedAttribute = $data->getAttributeByName($contentTypeField->getFieldId());
                $fieldValue = ($savedAttribute) ? $savedAttribute->getValue() : $contentTypeField->getDefaultValue();

                $this->addFieldToForm($contentTypeField, $form, $fieldValue);
            }
        }
    }

    /**
     * Add $contentTypeField to $form with value $fieldValue
     * 
     * @param FieldTypeInterface $contentTypeField
     * @param FormInterface      $form
     * @param mixed              $fieldValue
     */
    protected function addFieldToForm(FieldTypeInterface $contentTypeField, FormInterface $form, $fieldValue)
    {
        $form->add(
            $contentTypeField->getFieldId(),
            $this->fieldTypesConfiguration[$contentTypeField->getType()]['type'],
            array_merge(
                array(
                    'data' => $fieldValue,
                    'label' => $this->translationChoiceManager->choose($contentTypeField->getLabels()),
                    'mapped' => false,
                ),
                $this->getFieldOptions($contentTypeField)
            )
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
        $savedOptions = $contentTypeField->getFormOptions();
        $configuratedOptions = $this->fieldTypesConfiguration[$contentTypeField->getType()]['options'];
        $options = array();

        foreach ($configuratedOptions as $optionName => $optionConfiguration) {
            $options[$optionName] = (isset($savedOptions[$optionName])) ? $savedOptions[$optionName] : $optionConfiguration['default_value'];
        }

        return $options;
    }
}
