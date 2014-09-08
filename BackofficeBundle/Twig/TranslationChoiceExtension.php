<?php

namespace PHPOrchestra\BackofficeBundle\Twig;

use Doctrine\Common\Collections\Collection;
use PHPOrchestra\Backoffice\Manager\TranslationChoiceManager;

/**
 * Class TranslationChoiceExtension
 */
class TranslationChoiceExtension extends \Twig_Extension
{
    protected $translationChoiceManager;

    /**
     * @param TranslationChoiceManager $translationChoiceManager
     */
    public function __construct(TranslationChoiceManager $translationChoiceManager)
    {
        $this->translationChoiceManager = $translationChoiceManager;
    }

    /**
     * @param Collection $collection
     *
     * @return string
     */
    public function transChoose(Collection $collection)
    {
        return $this->translationChoiceManager->choose($collection);
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('trans_choose', array($this, 'transChoose')),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'translation_choice';
    }
}
