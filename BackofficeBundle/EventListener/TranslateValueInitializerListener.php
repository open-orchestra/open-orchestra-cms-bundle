<?php

namespace OpenOrchestra\BackofficeBundle\EventListener;

use OpenOrchestra\Backoffice\Initializer\TranslatedValueDefaultValueInitializer;
use OpenOrchestra\ModelInterface\Model\TranslatedValueContainerInterface;
use Symfony\Component\Form\FormEvent;

/**
 * Class TranslateValueInitializerListener
 */
class TranslateValueInitializerListener
{
    protected $fieldTypeClass;
    protected $translatedValueDefaultValueInitializer;

    /**
     * @param TranslatedValueDefaultValueInitializer $translatedValueDefaultValueInitializer
     * @param string                                 $fieldTypeClass
     */
    public function __construct(TranslatedValueDefaultValueInitializer $translatedValueDefaultValueInitializer, $fieldTypeClass)
    {
        $this->fieldTypeClass = $fieldTypeClass;
        $this->translatedValueDefaultValueInitializer = $translatedValueDefaultValueInitializer;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        /** @var TranslatedValueContainerInterface $data */
        $data = $event->getData();

        if ($data instanceof TranslatedValueContainerInterface) {
            $translatedProperties = $data->getTranslatedProperties();
            foreach ($translatedProperties as $property) {
                $properties = $data->$property();
                $this->translatedValueDefaultValueInitializer->generate($properties);
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
                $this->translatedValueDefaultValueInitializer->generate($properties);
            }
            $event->getForm()->setData($data);
        }
    }
}
