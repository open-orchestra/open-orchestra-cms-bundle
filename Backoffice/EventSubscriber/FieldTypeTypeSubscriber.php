<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
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
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        if ('PATCH' !== $form->getRoot()->getConfig()->getMethod()) {
            $data = $event->getData();
            if ($data instanceof FieldTypeInterface) {
                $type = $data->getType();

                $this->addOptionsFormType($data, $type, $form);
                $this->addDefaultValueFormType($data, $type, $form);
            }
        }
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $form->getData();

        if (is_null($data)) {
            $data = new $this->fieldTypeClass();
            $event->getForm()->setData($data);
        }

        $dataSend = $event->getData();
        $type = $dataSend['type'];
        $this->addOptionsFormType($data, $type, $form);
        $this->addDefaultValueFormType($data, $type, $form);
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * @param FieldTypeInterface $data
     * @param string             $type
     * @param FormInterface      $form
     */
    protected function addDefaultValueFormType(FieldTypeInterface $data, $type, FormInterface $form)
    {
        $container = $form->get('container');

        if ($container->has('default_value')) {
            $container->remove('default_value');
        }

        if (is_null($type) || !array_key_exists($type, $this->fieldTypeParameters)) {
            return;
        }

        if ($data->getType() !== $type) {
            $data->setDefaultValue(null);
        }

        if (isset($this->fieldTypeParameters[$type]['default_value'])) {
            $defaultValueField = $this->fieldTypeParameters[$type]['default_value'];
            $defaultOption = (isset($defaultValueField['options'])) ? $defaultValueField['options'] : array();
            $defaultOption['data'] = $data->getDefaultValue();
            $container->add('default_value', $defaultValueField['type'], $defaultOption);
            if (array_key_exists('search', $this->fieldTypeParameters[$type])) {
                $data->setFieldTypeSearchable($this->fieldTypeParameters[$type]['search']);
            }
        }
    }

    /**
     * @param FormEvent     $event
     */
    /**
     * @param FieldTypeInterface $data
     * @param string             $type
     * @param FormInterface      $form
     */
    protected function addOptionsFormType(FieldTypeInterface $data, $type, FormInterface $form)
    {
        if (is_null($type) || !array_key_exists($type, $this->fieldTypeParameters)) {
            return;
        }
        $container = $form->get('options');

        foreach ($container->all() as  $child) {
            $container->remove($child->getName());
        }

        if (array_key_exists('options', $this->fieldTypeParameters[$type])) {
            $fieldOptions = array();
            foreach ($data->getOptions() as $fieldOption) {
                $fieldOptions[$fieldOption->getKey()] = $fieldOption->getValue();
            }

            foreach ($this->fieldTypeParameters[$type]['options'] as $child => $option) {
                $value = $option['default_value'];
                if (array_key_exists($child, $fieldOptions)) {
                    $value = $fieldOptions[$child];
                }
                $this->addOptionFormType($container, $child, $value);
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
