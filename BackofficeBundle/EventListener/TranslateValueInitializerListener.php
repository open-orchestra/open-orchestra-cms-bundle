<?php

namespace PHPOrchestra\BackofficeBundle\EventListener;

use Doctrine\Common\Collections\Collection;
use PHPOrchestra\ModelBundle\Document\TranslatedValue;
use PHPOrchestra\ModelInterface\Model\TranslatedValueContainerInterface;
use Symfony\Component\Form\FormEvent;

/**
 * Class TranslateValueInitializerListener
 */
class TranslateValueInitializerListener
{
    protected $defaultLanguages;

    /**
     * @param array $defaultLanguages
     */
    public function __construct(array $defaultLanguages)
    {
        $this->defaultLanguages = $defaultLanguages;
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
     * @param Collection $properties
     */
    protected function generateDefaultValues(Collection $properties)
    {
        foreach ($this->defaultLanguages as $defaultLanguage) {
            if (!$properties->exists(function ($key, $element) use ($defaultLanguage) {
                return $defaultLanguage == $element->getLanguage();
            })
            ) {
                // TODO use a parameter
                $translatedValue = new TranslatedValue();
                $translatedValue->setLanguage($defaultLanguage);
                $properties->add($translatedValue);
            }
        }
    }
}
