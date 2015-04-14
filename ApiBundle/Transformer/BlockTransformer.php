<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BackofficeBundle\DisplayIcon\DisplayManager;
use OpenOrchestra\ApiBundle\Facade\BlockFacade;
use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\BackofficeBundle\StrategyManager\BlockParameterManager;
use OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;
use OpenOrchestra\DisplayBundle\DisplayBlock\DisplayBlockManager;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;

/**
 * Class BlockTransformer
 */
class BlockTransformer extends AbstractTransformer
{
    protected $blockParameterManager;
    protected $generateFormManager;
    protected $displayBlockManager;
    protected $nodeRepository;
    protected $displayManager;
    protected $blockClass;

    /**
     * @param DisplayBlockManager     $displayBlockManager
     * @param DisplayManager          $displayManager
     * @param string                  $blockClass
     * @param BlockParameterManager   $blockParameterManager
     * @param GenerateFormManager     $generateFormManager
     * @param NodeRepositoryInterface $nodeRepository
     */
    public function __construct(
        DisplayBlockManager $displayBlockManager,
        DisplayManager $displayManager,
        $blockClass,
        BlockParameterManager $blockParameterManager,
        GenerateFormManager   $generateFormManager,
        NodeRepositoryInterface $nodeRepository
    )
    {
        $this->blockParameterManager = $blockParameterManager;
        $this->generateFormManager = $generateFormManager;
        $this->displayBlockManager = $displayBlockManager;
        $this->displayIconManager = $displayManager;
        $this->nodeRepository = $nodeRepository;
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

        if (count($mixed->getAttributes()) > 0) {
            $html = $this->displayBlockManager->show($mixed)->getContent();
        } else {
            $html = $this->displayIconManager->show($mixed->getComponent());
        }

        $facade->uiModel = $this->getTransformer('ui_model')->transform(array(
            'label' => $mixed->getLabel()?: $mixed->getComponent(),
            'html' => $html
        ));

        if (!is_null($nodeId) && !is_null($blockNumber) && !is_null($nodeMongoId)) {
            $facade->addLink('_self_form', $this->generateRoute('open_orchestra_backoffice_block_form',
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
        } elseif (!is_null($facade->nodeId) && !is_null($facade->blockId)) {
            $block['blockId'] = $facade->blockId;
            $block['nodeId'] = $facade->nodeId;
            $blockElement = null;
            if (!is_null($node)) {
                if ($facade->nodeId == $node->getNodeId()) {
                    $block['nodeId'] = 0;
                    $blockElement = $node->getBlock($facade->blockId);
                } elseif ($facade->nodeId != $node->getNodeId()) {
                    $blockNode = $this->nodeRepository->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion($block['nodeId'], $node->getLanguage());
                    $blockElement = $blockNode->getBlock($facade->blockId);
                }
            }
        }

        if ($blockElement) {
            $block['blockParameter'] = $this->blockParameterManager->getBlockParameter($blockElement);
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
