<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\BlockFacade;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\CMSBundle\DisplayBlock\DisplayBlockManager;
use PHPOrchestra\ModelBundle\Document\Block;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use PHPOrchestra\ModelBundle\Model\NodeInterface;

/**
 * Class BlockTransformer
 */
class BlockTransformer extends AbstractTransformer
{
    protected $displayBlockManager;

    /**
     * @param DisplayBlockManager $displayBlockManager
     */
    public function __construct(DisplayBlockManager $displayBlockManager)
    {
        $this->displayBlockManager = $displayBlockManager;
    }

    /**
     * @param BlockInterface $mixed
     * @param boolean        $isInside
     *
     * @return FacadeInterface
     */
    public function transform($mixed, $isInside = true)
    {
        $facade = new BlockFacade();

        $facade->method = $isInside ? BlockFacade::GENERATE : BlockFacade::LOAD;
        $facade->component = $mixed->getComponent();

        foreach ($mixed->getAttributes() as $key => $attribute) {
            if (is_array($attribute)) {
                $facade->addAttribute($key, json_encode($attribute));
            } else {
                $facade->addAttribute($key, $attribute);
            }
        }

        $html = $this->displayBlockManager->showBack($mixed)->getContent();
        $facade->uiModel = $this->getTransformer('ui_model')->transform(array(
            'label' => $mixed->getComponent(),
            'html' => $html
        ));

        return $facade;
    }

    /**
     * @param BlockFacade|FacadeInterface $facade
     * @param NodeInterface|null          $node
     *
     * @return array
     */
    public function reverseTransform(FacadeInterface $facade, $node = null)
    {
        $blockArray = array(
            'nodeId' => $facade->nodeId,
            'blockId' => $facade->blockId
        );

        if (BlockFacade::GENERATE == $facade->method && null !== $node) {
            if (null === ($block = $node->getBlocks()->get($facade->blockId))) {
                $block = new Block();
            }
            $block->setComponent($facade->component);
            $block->setAttributes($facade->getAttributes());

            $blockArray['block'] = $block;
        }

        return $blockArray;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'block';
    }
}
