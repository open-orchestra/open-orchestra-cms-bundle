<?php

namespace OpenOrchestra\BackofficeBundle\EventListener;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\ModelInterface\Model\TranslatedValueContainerInterface;
use OpenOrchestra\ModelInterface\Model\TranslatedValueInterface;
use Symfony\Component\Form\FormEvent;

/**
 * Class TranslateValueInitializerListener
 */
class TranslateValueInitializerListener
{
    protected $fieldTypeClass;
    protected $defaultLanguages;
    protected $translatedValueClass;

    /**
     * @param array  $defaultLanguages
     * @param string $translatedValueClass
     * @param string $fieldTypeClass
     */
    public function __construct(array $defaultLanguages, $translatedValueClass, $fieldTypeClass)
    {
        $this->fieldTypeClass = $fieldTypeClass;
        $this->defaultLanguages = $defaultLanguages;
        $this->translatedValueClass = $translatedValueClass;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        /** @var TranslatedValueContainerInterface $data */
        $data = $event->getData();

        if ($data) {
            $translatedProperties = $data->getTranslatedProperties();
            foreach ($translatedProperties as $property) {
                $properties = $data->$property();
                $this->generateDefaultValues($properties);
            }
        }
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmitFieldType(FormEvent $event)
    {
        /** @var TranslatedValueContainerInterface $data */
        $data = $event->getForm()->getData();
        if (is_null($data)) {
            $fieldTypeClass = $this->fieldTypeClass;
            $data = new $fieldTypeClass();
            $translatedProperties = $data->getTranslatedProperties();
            foreach ($translatedProperties as $property) {
                $properties = $data->$property();
                $this->generateDefaultValues($properties);
            }
            $event->getForm()->setData($data);
        }
    }

    /**
     * @param Collection $properties
     */
    protected function generateDefaultValues(Collection $properties)
    {
        foreach ($this->defaultLanguages as $defaultLanguage) {
            if (!$properties->exists(function ($key,TranslatedValueInterface $element) use ($defaultLanguage) {
                return $defaultLanguage == $element->getLanguage();
            })
            ) {
                $translatedValueClass = $this->translatedValueClass;
                /** @var TranslatedValueInterface $translatedValue */
                $translatedValue = new $translatedValueClass();
                $translatedValue->setLanguage($defaultLanguage);
                $properties->add($translatedValue);
            }
        }
    }
}
