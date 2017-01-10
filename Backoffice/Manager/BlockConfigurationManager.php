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
     * @param string $category
     *
     * @return array
     */
    public function getComponentsWithCategory($category)
    {
        $components = array();
        foreach ($this->blockConfiguration as $blockComponent => $configuration) {
            if (isset($configuration['category']) && $category === $configuration['category']) {
                $components[] = $blockComponent;
            }
        }

        return $components;
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

        return $category;
    }

    /**
     * @param string $blockComponent
     *
     * @return string
     */
    public function getBlockComponentName($blockComponent)
    {
        $name = $blockComponent;

        if (
            isset($this->blockConfiguration[$blockComponent]) &&
            isset($this->blockConfiguration[$blockComponent]['name'])
        ) {
            $name = $this->blockConfiguration[$blockComponent]['name'];
        }

        return $this->translator->trans($name);
    }

    /**
     * @param string $blockComponent
     *
     * @return string
     */
    public function getBlockComponentDescription($blockComponent)
    {
        $description = '';

        if (
            isset($this->blockConfiguration[$blockComponent]) &&
            isset($this->blockConfiguration[$blockComponent]['description'])
        ) {
            $description = $this->blockConfiguration[$blockComponent]['description'];
        }

        return $this->translator->trans($description);
    }
}
