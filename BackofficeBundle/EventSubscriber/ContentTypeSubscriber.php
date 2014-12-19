<?php

namespace PHPOrchestra\BackofficeBundle\EventSubscriber;

use PHPOrchestra\Backoffice\Manager\TranslationChoiceManager;
use PHPOrchestra\ModelInterface\Model\FieldTypeInterface;
use PHPOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class ContentTypeSubscriber
 */
class ContentTypeSubscriber implements EventSubscriberInterface
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
                if ($attribute) {
                    $defaultValue = $attribute->getValue();
                } else {
                    $defaultValue = $field->getDefaultValue();
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
                if ($attribute = $content->getAttributeByName($field->getFieldId())) {
                    $attribute->setValue($data[$field->getFieldId()]);
                } else {
                    $contentAttributClass = $this->contentAttributClass;
                    $attribute = new $contentAttributClass;
                    $attribute->setName($field->getFieldId());
                    $attribute->setValue($data[$field->getFieldId()]);
                    $content->addAttribute($attribute);
                }
            }
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit'
        );
    }
}
