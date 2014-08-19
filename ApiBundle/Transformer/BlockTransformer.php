<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\BlockFacade;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockManager;
use PHPOrchestra\ModelBundle\Document\Block;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use PHPOrchestra\ModelBundle\Model\NodeInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
     * @param string|null    $nodeId
     * @param int|null       $blockNumber
     *
     * @return FacadeInterface
     */
    public function transform($mixed, $isInside = true, $nodeId = null, $blockNumber = null)
    {
        $facade = new BlockFacade();

        $facade->method = $isInside ? BlockFacade::GENERATE : BlockFacade::LOAD;
        $facade->component = $mixed->getComponent();

        $label = $mixed->getComponent();
        foreach ($mixed->getAttributes() as $key => $attribute) {
            if (is_array($attribute)) {
                $facade->addAttribute($key, json_encode($attribute));
            } else {
                $facade->addAttribute($key, $attribute);
            }
            if ('title' == $key) {
                $label = $attribute;
            }
        }

        $html = $this->displayBlockManager->show($mixed)->getContent();
        $facade->uiModel = $this->getTransformer('ui_model')->transform(array(
            'label' => $label ,
            'html' => $html
        ));

        if (null !== $nodeId && null !== $blockNumber) {
            $facade->addLink('_self_form', $this->getRouter()->generate('php_orchestra_backoffice_block_form',
                array(
                    'nodeId' => $nodeId,
                    'blockNumber' => $blockNumber
                ),
                UrlGeneratorInterface::ABSOLUTE_URL
            ));
        }

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
        $nodeId = (isset($facade->nodeId))? $facade->nodeId: 0;
        $blockId = (isset($facade->blockId))?$facade->blockId: $node->getBlocks()->count();

        $blockArray = array(
            'nodeId' => $nodeId,
            'blockId' => $blockId
        );

        if (BlockFacade::GENERATE == $facade->method && null !== $node) {
            if (null === ($block = $node->getBlocks()->get($blockId))) {
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
