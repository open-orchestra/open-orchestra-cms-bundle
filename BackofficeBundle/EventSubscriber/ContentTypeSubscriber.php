<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
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
     * @param array                          $fieldTypesConfiguration
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
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $contentType = $this->contentTypeRepository->findOneByContentTypeIdInLastVersion($data->getContentType());

        if (is_object($contentType)) {
            $data->setContentTypeVersion($contentType->getVersion());
            $this->addContentTypeFieldsToForm($contentType->getFields(), $event->getForm(), $data);
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

        if (is_object($contentType)) {
            $data = $event->getData();
            $content->setContentTypeVersion($contentType->getVersion());
            foreach ($contentType->getFields() as $contentTypeField) {
                $contentTypeFieldId = $contentTypeField->getFieldId();
                if ($attribute = $content->getAttributeByName($contentTypeFieldId)) {
                    $attribute->setValue($this->transformData($data[$contentTypeFieldId], $form->get($contentTypeFieldId)));
                } elseif (is_null($attribute)) {
                    $contentAttributClass = $this->contentAttributClass;
                    $attribute = new $contentAttributClass;
                    $attribute->setName($contentTypeFieldId);
                    $attribute->setValue($this->transformData($data[$contentTypeFieldId], $form->get($contentTypeFieldId)));
                    $content->addAttribute($attribute);
                }
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
                $dataAttribute = $data->getAttributeByName($contentTypeField->getFieldId());
                $fieldValue = ($dataAttribute) ? $dataAttribute->getValue() : $contentTypeField->getDefaultValue();
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
        $fieldTypeConfiguration = $this->fieldTypesConfiguration[$contentTypeField->getType()];

        $fieldParameters = array_merge(
            array(
                'data' => $fieldValue,
                'label' => $this->translationChoiceManager->choose($contentTypeField->getLabels()),
                'mapped' => false,
            ),
            $this->getFieldOptions($contentTypeField)
        );

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
