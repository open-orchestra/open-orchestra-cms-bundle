<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\BackofficeBundle\DisplayIcon\DisplayManager;
use PHPOrchestra\ApiBundle\Facade\BlockFacade;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\BackofficeBundle\StrategyManager\BlockParameterManager;
use PHPOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockManager;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use PHPOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class BlockTransformer
 */
class BlockTransformer extends AbstractTransformer
{
    protected $blockParameterManager;
    protected $generateFormManager;
    protected $displayBlockManager;
    protected $displayManager;
    protected $blockClass;

    /**
     * @param DisplayBlockManager   $displayBlockManager
     * @param DisplayManager        $displayManager
     * @param string                $blockClass
     * @param BlockParameterManager $blockParameterManager
     * @param GenerateFormManager   $generateFormManager
     */
    public function __construct(DisplayBlockManager $displayBlockManager, DisplayManager $displayManager, $blockClass, BlockParameterManager $blockParameterManager, GenerateFormManager $generateFormManager)
    {
        $this->blockParameterManager = $blockParameterManager;
        $this->generateFormManager = $generateFormManager;
        $this->displayBlockManager = $displayBlockManager;
        $this->displayIconManager = $displayManager;
        $this->blockClass = $blockClass;
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
        $facade->class = $mixed->getClass();
        $facade->id = $mixed->getId();
        $facade->nodeId = $nodeId;
        $facade->blockId = $blockNumber;

        foreach ($mixed->getAttributes() as $key => $attribute) {
            if (is_array($attribute)) {
                $attribute = json_encode($attribute);
            }
            $facade->addAttribute($key, $attribute);
        }

        $html = $this->displayIconManager->show($mixed->getComponent());
        if (count($mixed->getAttributes()) > 0) {
            $html = $this->displayBlockManager->show($mixed)->getContent();
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
        $block  = array(
            'blockParameter' => array()
        );

        if (!is_null($facade->component)) {
            $blockClass = $this->blockClass;
            /** @var BlockInterface $blockElement */
            $blockElement = new $blockClass();
            $blockElement->setComponent($facade->component);
            $blockElement->setAttributes($this->generateFormManager->getDefaultConfiguration($blockElement));
            $node->addBlock($blockElement);
            $blockIndex = $node->getBlockIndex($blockElement);
            $block['blockId'] = $blockIndex;
            $block['nodeId'] = 0;
            $block['blockParameter'] = $this->blockParameterManager->getBlockParameter($blockElement);
        } elseif (!is_null($facade->nodeId) && !is_null($facade->blockId)) {
            $block['blockId'] = $facade->blockId;
            $block['nodeId'] = $facade->nodeId;
            if (!is_null($node)) {
                if ($facade->nodeId == $node->getNodeId()) {
                    $block['nodeId'] = 0;
                }
                $block['blockParameter'] = $this->blockParameterManager->getBlockParameter($node->getBlock($facade->blockId));
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
