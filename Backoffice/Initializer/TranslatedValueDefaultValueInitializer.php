<?php

namespace OpenOrchestra\Backoffice\Initializer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\ModelInterface\Model\TranslatedValueInterface;

@trigger_error('The '.__NAMESPACE__.'\EmbedKeyword class is deprecated since version 1.2.0 and will be removed in 2.0', E_USER_DEPRECATED);

/**
 * Class TranslatedValueDefaultValueInitializer
 *
 * @deprecated will be removed in 2.0
 */
class TranslatedValueDefaultValueInitializer implements DefaultValueInitializerInterface
{
    protected $defaultLanguages;
    protected $translatedValueClass;

    /**
     * @param array  $defaultLanguages
     * @param string $translatedValueClass
     */
    public function __construct(array $defaultLanguages, $translatedValueClass)
    {
        $this->defaultLanguages = $defaultLanguages;
        $this->translatedValueClass = $translatedValueClass;
    }

    /**
     * @param Collection $properties
     */
    public function generate(Collection $properties)
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
                $properties->set($defaultLanguage, $translatedValue);
            }
        }
    }
}
