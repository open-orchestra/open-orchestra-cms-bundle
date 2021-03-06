<?php

namespace OpenOrchestra\Backoffice\Manager;

use OpenOrchestra\Backoffice\Context\ContextBackOfficeInterface;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class MultiLanguagesChoiceManager
 */
class MultiLanguagesChoiceManager implements MultiLanguagesChoiceManagerInterface
{
    protected $contextManager;
    protected $translator;

    /**
     * @param ContextBackOfficeInterface $contextManager
     * @param TranslatorInterface        $translator
     */
    public function __construct(ContextBackOfficeInterface $contextManager, TranslatorInterface $translator)
    {
        $this->contextManager = $contextManager;
        $this->translator = $translator;
    }

    /**
     * @param array       $elements
     * @param string|null $language
     *
     * @return string
     */
    public function choose(array $elements, $language = null)
    {
        if (null === $language) {
            $language = $this->contextManager->getBackOfficeLanguage();
        }

        if (!isset($elements[$language])) {
            return $this->translator->trans('open_orchestra_backoffice.no_translation');
        }

        return $elements[$language];
    }
}
