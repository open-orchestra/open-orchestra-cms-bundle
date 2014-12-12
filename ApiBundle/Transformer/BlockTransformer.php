<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\BackofficeBundle\DisplayIcon\DisplayManager;
use PHPOrchestra\ModelBundle\Document\Block;
use Symfony\Component\Translation\TranslatorInterface;
use PHPOrchestra\ApiBundle\Facade\BlockFacade;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockManager;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use PHPOrchestra\ModelBundle\Model\NodeInterface;

/**
 * Class BlockTransformer
 */
class BlockTransformer extends AbstractTransformer
{
    protected $displayBlockManager;
    protected $displayManager;

    /**
     * @param DisplayBlockManager $displayBlockManager
     * @param DisplayManager $displayManager
     */
    public function __construct(DisplayBlockManager $displayBlockManager, DisplayManager $displayManager)
    {
        $this->displayBlockManager = $displayBlockManager;
        $this->displayIconManager = $displayManager;
    }

    /**
     * @param BlockInterface $mixed
     * @param boolean        $isInside
     * @param string|null    $nodeId
     * @param int|null       $blockNumber
     * @param int|null       $areaId
     * @param int|null       $blockPosition
     * @param string|null    $nodeMongoId
     *
     * @return FacadeInterface
     */
    public function transform($mixed, $isInside = true, $nodeId = null, $blockNumber = null, $areaId = 0, $blockPosition = 0, $nodeMongoId = null)
    {
        $facade = new BlockFacade();

        $facade->method = $isInside ? BlockFacade::GENERATE : BlockFacade::LOAD;
        $facade->component = $mixed->getComponent();
        $facade->label = $mixed->getLabel();
        $facade->nodeId = $nodeId;
        $facade->blockId = $blockNumber;

        foreach ($mixed->getAttributes() as $key => $attribute) {
            if (is_array($attribute)) {
                $facade->addAttribute($key, json_encode($attribute));
            } else {
                $facade->addAttribute($key, $attribute);
            }
        }

        if (count($mixed->getAttributes()) > 0) {
            $html = $this->displayBlockManager->show($mixed)->getContent();
        } else {
            $html = $this->displayIconManager->show($mixed->getComponent());
        }

        $facade->uiModel = $this->getTransformer('ui_model')->transform(array(
            'label' => $mixed->getLabel()?: $mixed->getComponent(),
            'html' => $html
        ));

        if (!is_null($nodeId) && !is_null($blockNumber)) {
            $facade->addLink('_self_form', $this->generateRoute('php_orchestra_backoffice_block_form',
                array(
                    'nodeId' => $nodeMongoId,
                    'blockNumber' => $blockNumber
                )
            ));
        }

        return $facade;
    }

    /**
     * @param FacadeInterface|BlockFacade $facade
     * @param NodeInterface|null          $node
     *
     * @return array
     */
    public function reverseTransformToArray(FacadeInterface $facade, NodeInterface $node = null)
    {
        $block  = array();

        if (!is_null($facade->component)) {
            $newBlock = new Block();
            $newBlock->setComponent($facade->component);
            $node->addBlock($newBlock);
            $blockIndex = $node->getBlockIndex($newBlock);
            $block['blockId'] = $blockIndex;
            $block['nodeId'] = 0;
        } elseif (!is_null($facade->nodeId) && !is_null($facade->blockId)) {
            $block['blockId'] = $facade->blockId;
            if (!is_null($node) && ($facade->nodeId == $node->getNodeId())) {
                $block['nodeId'] = 0;
            } else {
                $block['nodeId'] = $facade->nodeId;
            }
        }

        return $block;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'block';
    }
}
