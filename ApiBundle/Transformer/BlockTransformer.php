<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\Backoffice\DisplayIcon\DisplayManager;
use OpenOrchestra\ApiBundle\Facade\BlockFacade;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\BackofficeBundle\StrategyManager\BlockParameterManager;
use OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\DisplayBundle\DisplayBlock\DisplayBlockManager;
use OpenOrchestra\ModelInterface\BlockNodeEvents;
use OpenOrchestra\ModelInterface\Event\BlockNodeEvent;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class BlockTransformer
 */
class BlockTransformer extends AbstractTransformer
{
    protected $blockParameterManager;
    protected $generateFormManager;
    protected $displayBlockManager;
    protected $displayBlockFrontManager;
    protected $displayIconManager;
    protected $currentSiteManager;
    protected $eventDispatcher;
    protected $nodeRepository;
    protected $displayManager;
    protected $blockClass;
    protected $translator;

    /**
     * @param string                   $facadeClass
     * @param DisplayBlockManager      $displayBlockManager
     * @param DisplayBlockManager      $displayBlockFrontManager
     * @param DisplayManager           $displayManager
     * @param string                   $blockClass
     * @param BlockParameterManager    $blockParameterManager
     * @param GenerateFormManager      $generateFormManager
     * @param NodeRepositoryInterface  $nodeRepository
     * @param CurrentSiteIdInterface   $currentSiteManager
     * @param TranslatorInterface      $translator
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        $facadeClass,
        DisplayBlockManager $displayBlockManager,
        DisplayBlockManager $displayBlockFrontManager,
        DisplayManager $displayManager,
        $blockClass,
        BlockParameterManager $blockParameterManager,
        GenerateFormManager   $generateFormManager,
        NodeRepositoryInterface $nodeRepository,
        CurrentSiteIdInterface $currentSiteManager,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher
    )
    {
        parent::__construct($facadeClass);
        $this->blockParameterManager = $blockParameterManager;
        $this->generateFormManager = $generateFormManager;
        $this->displayBlockManager = $displayBlockManager;
        $this->displayBlockFrontManager = $displayBlockFrontManager;
        $this->displayIconManager = $displayManager;
        $this->nodeRepository = $nodeRepository;
        $this->blockClass = $blockClass;
        $this->currentSiteManager = $currentSiteManager;
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param BlockInterface $block
     * @param boolean        $isInside
     * @param string|null    $nodeId
     * @param int|null       $blockNumber
     * @param int            $areaId
     * @param int            $blockPosition
     * @param string|null    $nodeMongoId
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform(
        $block,
        $isInside = true,
        $nodeId = null,
        $blockNumber = null,
        $areaId = 0,
        $blockPosition = 0,
        $nodeMongoId = null
    )
    {
        if (!$block instanceof BlockInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->method = $isInside ? BlockFacade::GENERATE : BlockFacade::LOAD;
        $facade->component = $block->getComponent();
        $facade->label = $block->getLabel();
        $facade->class = $block->getClass();
        $facade->id = $block->getId();
        $facade->nodeId = $nodeId;
        $facade->blockId = $blockNumber;

        foreach ($block->getAttributes() as $key => $attribute) {
            if (is_array($attribute)) {
                $attribute = json_encode($attribute);
            }
            $facade->addAttribute($key, $attribute);
        }

        if (count($block->getAttributes()) > 0) {
            $html = $this->displayBlockManager->show($block)->getContent();
        } else {
            $html = $this->displayIconManager->show($block->getComponent());
        }

        $facade->uiModel = $this->getTransformer('ui_model')->transform(array(
            'label' => $block->getLabel()?: $this->translator->trans('open_orchestra_backoffice.block.' . $block->getComponent() . '.title'),
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

        if (null !== $nodeId &&
            null !== $nodeMongoId &&
            NodeInterface::TRANSVERSE_NODE_ID === $nodeId &&
            $isInside
        ) {
            $facade->isDeletable = $this->blockIsDeletable($block, $nodeMongoId);
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
        $blockElement = null;
        if (!is_null($facade->nodeId) && !is_null($facade->blockId)) {
            $block['blockId'] = $facade->blockId;
            $block['nodeId'] = $facade->nodeId;
            if (!is_null($node)) {
                if ($facade->nodeId == $node->getNodeId()) {
                    $block['nodeId'] = 0;
                    $blockElement = $node->getBlock($facade->blockId);
                } elseif ($facade->nodeId != $node->getNodeId()) {
                    $siteId = $this->currentSiteManager->getCurrentSiteId();
                    $blockNode = $this->nodeRepository->findInLastVersion($block['nodeId'], $node->getLanguage(), $siteId);
                    $blockElement = $blockNode->getBlock($facade->blockId);
                }
            }
        } elseif (!is_null($facade->component)) {
            $blockClass = $this->blockClass;
            /** @var BlockInterface $blockElement */
            $blockElement = new $blockClass();
            $blockElement->setComponent($facade->component);
            $blockElement->setAttributes($this->generateFormManager->getDefaultConfiguration($blockElement));
            $node->addBlock($blockElement);
            $blockIndex = $node->getBlockIndex($blockElement);
            $block['blockId'] = $blockIndex;
            $block['nodeId'] = 0;
            $this->eventDispatcher->dispatch(BlockNodeEvents::ADD_BLOCK_TO_NODE, new BlockNodeEvent($node, $blockElement));
        }

        if ($blockElement) {
            $block['blockParameter'] = $this->blockParameterManager->getBlockParameter($blockElement);
            $block['blockPrivate'] = !$this->displayBlockFrontManager->isPublic($blockElement);
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

    /**
     * @param BlockInterface $block
     * @param string         $nodeMongoId
     *
     * @return bool
     */
    protected function blockIsDeletable(BlockInterface $block, $nodeMongoId)
    {
        foreach ($block->getAreas() as $area) {
            if (isset($area['nodeId']) &&
                0 !== $area['nodeId'] &&
                $nodeMongoId !== $area['nodeId']
            ) {
                return false;
            }
        }

        return true;
    }
}
