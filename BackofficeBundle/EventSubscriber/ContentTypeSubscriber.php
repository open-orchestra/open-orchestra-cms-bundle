<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use Symfony\Component\Form\FormEvent;

/**
 * Class ContentTypeSubscriber
 */
class ContentTypeSubscriber extends AbstractBlockContentTypeSubscriber
{
    protected $translationChoiceManager;
    protected $contentTypeRepository;
    protected $contentAttributClass;

    /**
     * @param ContentTypeRepositoryInterface $contentTypeRepository
     * @param string                         $contentAttributClass
     * @param TranslationChoiceManager       $translationChoiceManager
     */
    public function __construct(
        ContentTypeRepositoryInterface $contentTypeRepository,
        $contentAttributClass,
        TranslationChoiceManager $translationChoiceManager
    )
    {
        $this->contentTypeRepository = $contentTypeRepository;
        $this->contentAttributClass = $contentAttributClass;
        $this->translationChoiceManager = $translationChoiceManager;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $contentType = $this->contentTypeRepository->findOneByContentTypeIdAndVersion($data->getContentType(), $data->getContentTypeVersion());

        if (is_object($contentType)) {
            $data->setContentTypeVersion($contentType->getVersion());
            /** @var FieldTypeInterface $field */
            foreach ($contentType->getFields() as $field) {
                $attribute = $data->getAttributeByName($field->getFieldId());
                $defaultValue = $field->getDefaultValue();
                if ($attribute) {
                    $defaultValue = $attribute->getValue();
                }
                $form->add($field->getFieldId(), $field->getType(), array_merge(
                    array(
                        'data' => $defaultValue,
                        'label' => $this->translationChoiceManager->choose($field->getLabels()),
                        'mapped' => false,
                    ),
                    $field->getFormOptions()
                ));
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
        $data = $event->getData();
        $contentType = $this->contentTypeRepository->findOneByContentTypeIdAndVersion($content->getContentType(), $content->getContentTypeVersion());

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
}
