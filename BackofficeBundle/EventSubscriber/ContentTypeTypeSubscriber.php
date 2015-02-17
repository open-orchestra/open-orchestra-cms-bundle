<?php

namespace PHPOrchestra\BackofficeBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use PHPOrchestra\ModelInterface\Model\FieldTypeInterface;
use Symfony\Component\Form\FormEvent;


use PHPOrchestra\ModelBundle\Document\TranslatedValue;
use PHPOrchestra\ModelBundle\Document\FieldOption;


/**
 * Class ContentTypeTypeSubscriber
 */
class ContentTypeTypeSubscriber implements EventSubscriberInterface
{
    protected $fieldClass;

    /**
     * @param string $fieldClass
     */
    public function __construct($fieldClass)
    {
        $this->fieldClass = $fieldClass;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $contentType = $form->getData();
        $data = $event->getData();

        $fields = $contentType->getFields();
        foreach ($fields as $field) {
            $contentType->removeFieldType($field);
        }

        foreach ($data['fields'] as $value) {
            $fieldClass = $this->fieldClass;
            $field = new $fieldClass();
            $field->setFieldId($value['fieldId']);

            foreach ($value['labels'] as $subValue) {
                $translatedValue = new TranslatedValue();
                $translatedValue->setLanguage($subValue['language']);
                $translatedValue->setValue($subValue['value']);
                $field->addLabel($translatedValue);
            }

            $field->setDefaultValue($value['defaultValue']);
            $field->setSearchable($value['searchable']);
            $field->setType($value['type']);

            foreach ($value['options'] as $subValue) {
                $fieldOption = new FieldOption();
                $fieldOption->setKey($subValue['key']);
                $fieldOption->setValue($subValue['value']);
                $field->addOption($fieldOption);
            }
            $contentType->addFieldType($field);
        }
    }
}
