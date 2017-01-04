<?php

namespace OpenOrchestra\Backoffice\Manager;

use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class BlockConfigurationManager
 */
class BlockConfigurationManager
{
    const DEFAULT_CATEGORY = 'open_orchestra_backoffice.block_configuration.category.default';

    protected $blockConfiguration;
    protected $translator;

    /**
     * @param array               $blockConfiguration
     * @param TranslatorInterface $translator
     */
    public function __construct(array $blockConfiguration, TranslatorInterface $translator)
    {
        $this->blockConfiguration = $blockConfiguration;
        $this->translator = $translator;
    }

    /**
     * @param string $blockComponent
     *
     * @return string
     */
    public function getBlockCategory($blockComponent)
    {
        $category = self::DEFAULT_CATEGORY;

        if (
            isset($this->blockConfiguration[$blockComponent]) &&
            isset($this->blockConfiguration[$blockComponent]['category'])
        ) {
            $category = $this->blockConfiguration[$blockComponent]['category'];
        }

        return $this->translator->trans($category);
    }
}
