<?php

namespace OpenOrchestra\Backoffice\Manager;

use Doctrine\Common\Util\Inflector;
use OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;
use OpenOrchestra\ModelInterface\Model\BlockInterface;

/**
 * Class BlockManager
 */
class BlockManager
{
    protected $blockClass;
    protected $generateFormManager;
    protected $fixedParameters;

    /**
     * Constructor
     *
     * @param string                $blockClass
     * @param GenerateFormManager   $generateFormManager
     * @param array                 $fixedParameters
     */
    public function __construct(
        $blockClass,
        GenerateFormManager $generateFormManager,
        array $fixedParameters
    ){
        $this->blockClass = $blockClass;
        $this->generateFormManager = $generateFormManager;
        $this->fixedParameters = $fixedParameters;
    }

    /**
     * @param string  $component
     * @param string  $siteId
     * @param string  $language
     * @param boolean $isTransverse
     *
     * @return BlockInterface
     */
    public function initializeBlock($component, $siteId, $language, $isTransverse)
    {
        /** @var BlockInterface $block */
        $block = new $this->blockClass();
        $block->setComponent($component);
        $block->setTransverse($isTransverse);
        $block->setSiteId($siteId);
        $block->setLanguage($language);
        $this->setDefaultAttributes($block);

        return $block;
    }

    /**
     * @param BlockInterface $block
     * @param string         $language
     *
     * @return BlockInterface
     */
    public function createToTranslateBlock(BlockInterface $block, $language)
    {
        $block = clone $block;
        $oldLanguage = $block->getLanguage();
        $block->setLanguage($language);
        $block->setLabel($block->getLabel()."[".$oldLanguage."]");

        return $block;
    }

    /**
     * @param BlockInterface $block
     */
    protected function setDefaultAttributes(BlockInterface $block)
    {
        $defaultConfiguration = $this->generateFormManager->getDefaultConfiguration($block);
        foreach ($defaultConfiguration as $key => $value) {
            if (in_array($key, $this->fixedParameters) &&
                method_exists($block, $setter = 'set' . Inflector::classify($key))
            ) {
                $block->$setter($value);
                unset($defaultConfiguration[$key]);
            }
        }
        $block->setAttributes($defaultConfiguration);
    }
}
