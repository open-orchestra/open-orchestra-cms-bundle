<?php

namespace OpenOrchestra\Backoffice\Manager;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\Backoffice\Context\ContextManager;
use OpenOrchestra\ModelInterface\Manager\TranslationChoiceManagerInterface;

/**
 * Class TranslationChoiceManager
 */
class TranslationChoiceManager implements TranslationChoiceManagerInterface
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
