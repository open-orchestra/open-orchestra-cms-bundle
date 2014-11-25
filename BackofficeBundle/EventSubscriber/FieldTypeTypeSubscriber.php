<?php

namespace PHPOrchestra\BackofficeBundle\EventSubscriber;

use PHPOrchestra\ModelBundle\Document\FieldOption;
use PHPOrchestra\ModelBundle\Model\FieldTypeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class FieldTypeTypeSubscriber
 */
class FieldTypeTypeSubscriber implements EventSubscriberInterface
{
    protected $options = array();

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        /** @var FieldTypeInterface $data */
        $form = $event->getForm();
        $data = $event->getData();
        if ($data instanceof FieldTypeInterface) {
            $type = $data->getType();

            $this->checkFieldType($data, $type, $form);
        }
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        /** @var FieldTypeInterface $data */
        $form = $event->getForm();
        $data = $form->getData();
        $dataSend = $event->getData();
        $type = $dataSend['type'];

        $this->checkFieldType($data, $type, $form);
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
     * @param Form               $form
     */
    protected function checkFieldType(FieldTypeInterface $data, $type, Form $form)
    {
        if ($data instanceof FieldTypeInterface && !is_null($type)) {
            if (array_key_exists($type, $this->options)) {
                $keys = array();
                foreach ($this->options[$type]['options'] as $key => $option) {
                    if (!$data->hasOption($key)) {
                        $fieldOption = new FieldOption();
                        $fieldOption->setKey($key);
                        $fieldOption->setValue($option['default_value']);

                        $data->addOption($fieldOption);
                    }
                    $keys[] = $key;
                }

                foreach ($data->getOptions() as $option) {
                    if (!in_array($option->getKey(), $keys)) {
                        $data->removeOption($option);
                    }
                }

                $form->add('options', 'collection', array(
                    'type' => 'field_option',
                    'allow_add' => false,
                    'allow_delete' => false,
                    'label' => 'php_orchestra_backoffice.form.field_type.options',
                ));
            }
        }
    }
}
