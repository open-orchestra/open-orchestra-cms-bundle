<?php

namespace OpenOrchestra\Backoffice\Manager;

use Doctrine\Common\Util\Inflector;
use OpenOrchestra\BackofficeBundle\StrategyManager\BlockParameterManager;
use OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;
use OpenOrchestra\DisplayBundle\DisplayBlock\DisplayBlockManager;
use OpenOrchestra\ModelInterface\Model\BlockInterface;

/**
 * Class BlockManager
 */
class BlockManager
{
    protected $blockClass;
    protected $displayBlockManager;
    protected $blockParameterManager;
    protected $generateFormManager;
    protected $fixedParameters;

    /**
     * Constructor
     *
     * @param string                $blockClass
     * @param DisplayBlockManager   $displayBlockManager
     * @param BlockParameterManager $blockParameterManager
     * @param GenerateFormManager   $generateFormManager
     * @param array                 $fixedParameters
     */
    public function __construct(
        $blockClass,
        DisplayBlockManager $displayBlockManager,
        BlockParameterManager $blockParameterManager,
        GenerateFormManager $generateFormManager,
        array $fixedParameters
    ){
        $this->blockClass = $blockClass;
        $this->displayBlockManager = $displayBlockManager;
        $this->blockParameterManager = $blockParameterManager;
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
        $block->setPrivate(!$this->displayBlockManager->isPublic($block));
        $block->setParameter($this->blockParameterManager->getBlockParameter($block));
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
