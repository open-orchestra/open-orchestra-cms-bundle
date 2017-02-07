<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormInterface;

/**
 * Class FieldTypeTypeSubscriber
 */
class FieldTypeTypeSubscriber implements EventSubscriberInterface
{
    protected $fieldOptions;
    protected $fieldTypeParameters;
    protected $fieldOptionClass;
    protected $fieldTypeClass;

    /**
     * @param array  $fieldOptions
     * @param array  $fieldTypeParameters
     * @param string $fieldOptionClass
     * @param string $fieldTypeClass
     */
    public function __construct(
        array $fieldOptions,
        array $fieldTypeParameters,
        $fieldOptionClass,
        $fieldTypeClass
    ) {
        $this->fieldOptions = $fieldOptions;
        $this->fieldTypeParameters = $fieldTypeParameters;
        $this->fieldOptionClass = $fieldOptionClass;
        $this->fieldTypeClass = $fieldTypeClass;
    }

    /**
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        $form = $event->getForm();
        if ('PATCH' !== $form->getRoot()->getConfig()->getMethod()) {
            $this->addFormType($event);
        }
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $this->addFormType($event);
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }


    /**
     * @param FormEvent $event
     */
    protected function addFormType(FormEvent $event)
    {
        $this->addOptionsFormType($event);
        $this->addDefaultValueFormType($event);
    }

    /**
     * @param FormEvent     $event
     */
    protected function addDefaultValueFormType(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $fieldType = $form->getData();
        $type = $fieldType->getType();
        $container = $form->get('container');

        if ($container->has('default_value')) {
            $container->remove('default_value');
        }
        $defaultValue = $form->getData()->getDefaultValue();

        if(is_array($data)) {
            $newType = array_key_exists('type', $data) ? $data['type'] : null;
            if (is_null($newType) || !array_key_exists($newType, $this->fieldTypeParameters)) {
                return;
            }
            $defaultValue = array_key_exists('default_value', $data) ? $data['default_value'] : $defaultValue;
            if ($newType !== $type) {
                $defaultValue = null;
            }
            $type = $newType;
        }
        if (isset($this->fieldTypeParameters[$type]['default_value'])) {
            $defaultValueField = $this->fieldTypeParameters[$type]['default_value'];
            $defaultOption = (isset($defaultValueField['options'])) ? $defaultValueField['options'] : array();
            $defaultOption['data'] = $defaultValue;
            $container->add('default_value', $defaultValueField['type'], $defaultOption);
            if (array_key_exists('search', $this->fieldTypeParameters[$type])) {
                $fieldType->setFieldTypeSearchable($this->fieldTypeParameters[$type]['search']);
                //$form->setData($fieldType);
            }
        }
    }

    /**
     * @param FormEvent     $event
     */
    protected function addOptionsFormType(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $container = $form->get('options');

        foreach ($container->all() as  $child) {
            $container->remove($child->getName());
        }

        if (is_array($data)) {
            $fieldType = array_key_exists('type', $data) ? $data['type'] : '';
            if (is_null($fieldType) || !array_key_exists($fieldType, $this->fieldTypeParameters) || !array_key_exists('options', $this->fieldTypeParameters[$fieldType])) {
                return;
            }
            foreach ($this->fieldTypeParameters[$fieldType]['options'] as $child => $option) {
                $this->addOptionFormType($container, $child, $option['default_value']);
            }
        } else {
            foreach ($data->getOptions() as $option) {
                $this->addOptionFormType($container, $option->getKey(), $option->getValue());
            }
        }
    }

    /**
     * @param FormInterface $form
     * @param string        $child
     * @param string        $data
     */
    protected function addOptionFormType(FormInterface $form, $child, $data) {
        if (is_null($child) || !array_key_exists($child, $this->fieldOptions)) {
            return;
        }
        $formTypeOptions = $this->fieldOptions[$child];
        $formTypeOptions['data'] = $data;
        $formType = $formTypeOptions['type'];
        if (array_key_exists('required', $formTypeOptions) && $formTypeOptions['required'] === true) {
            $formTypeOptions['constraints'] = new NotBlank();
        }
        unset($formTypeOptions['type']);
        $form->add($child, $formType, $formTypeOptions);
    }
}
