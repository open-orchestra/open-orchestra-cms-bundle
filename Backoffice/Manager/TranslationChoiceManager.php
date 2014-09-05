<?php

namespace PHPOrchestra\Backoffice\Manager;

use Doctrine\Common\Collections\Collection;
use PHPOrchestra\Backoffice\Context\ContextManager;

/**
 * Class TranslationChoiceManager
 */
class TranslationChoiceManager
{
    protected $currentLocale;

    /**
     * @param ContextManager $contextManager
     */
    public function __construct(ContextManager $contextManager)
    {
        $this->currentLocale = $contextManager->getCurrentLocale();
    }

    /**
     * @param Collection $collection
     *
     * @return string
     */
    public function choose(Collection $collection)
    {
        if ($collection->isEmpty()) {
            return 'no translation';
        }

        $local = $this->currentLocale;

        foreach ($collection as $element) {
            if ($local == $element->getLanguage()) {
                return $element->getValue();
            }
        }

        return $collection->first()->getValue();
    }
}
