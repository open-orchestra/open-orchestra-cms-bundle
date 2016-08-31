<?php

namespace OpenOrchestra\Backoffice\Manager;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\Backoffice\Context\ContextManager;
use OpenOrchestra\ModelInterface\Manager\TranslationChoiceManagerInterface;

@trigger_error('The '.__NAMESPACE__.'\EmbedKeyword class is deprecated since version 1.2.0 and will be removed in 2.0, use MultiLanguagesChoiceManager', E_USER_DEPRECATED);

/**
 * Class TranslationChoiceManager
 *
 * @deprecated will be removed in 2.0, use MultiLanguagesChoiceManager
 */
class TranslationChoiceManager implements TranslationChoiceManagerInterface
{
    protected $contextManager;

    /**
     * @param ContextManager $contextManager
     */
    public function __construct(ContextManager $contextManager)
    {
        $this->contextManager = $contextManager;
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

        $local = $this->contextManager->getCurrentLocale();

        foreach ($collection as $element) {
            if ($local == $element->getLanguage()) {
                return $element->getValue();
            }
        }

        return $collection->first()->getValue();
    }
}
