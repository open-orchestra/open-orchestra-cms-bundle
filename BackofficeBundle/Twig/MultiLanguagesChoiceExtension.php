<?php

namespace OpenOrchestra\BackofficeBundle\Twig;

use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;

/**
 * Class MultiLanguagesChoiceExtension
 */
class MultiLanguagesChoiceExtension extends \Twig_Extension
{
    protected $multiLanguagesChoiceManager;

    /**
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     */
    public function __construct(MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager)
    {
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
    }

    /**
     * @param array $elements
     *
     * @return string
     */
    public function multiLanguageChoose(array $elements)
    {
        return $this->multiLanguagesChoiceManager->choose($elements);
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('multi_languages_choose', array($this, 'multiLanguageChoose')),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'multi_languages_choice';
    }
}
