<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\Backoffice\Manager\BlockConfigurationManager;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\Backoffice\DisplayBlock\DisplayBlockManager;
use OpenOrchestra\ModelBundle\Repository\NodeRepository;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;

/**
 * Class BlockTransformer
 */
class BlockTransformer extends AbstractTransformer
{
    protected $displayBlockManager;
    /** @var  NodeRepositoryInterface */
    protected $nodeRepository;
    protected $blockConfigurationManager;

    /**
     * @param string                    $facadeClass
     * @param DisplayBlockManager       $displayBlockManager
     * @param NodeRepositoryInterface   $nodeRepository
     * @param BlockConfigurationManager $blockConfigurationManager
     */
    public function __construct(
        $facadeClass,
        DisplayBlockManager      $displayBlockManager,
        NodeRepositoryInterface  $nodeRepository,
        BlockConfigurationManager $blockConfigurationManager
    ) {
        parent::__construct($facadeClass);
        $this->displayBlockManager = $displayBlockManager;
        $this->nodeRepository = $nodeRepository;
        $this->blockConfigurationManager = $blockConfigurationManager;
    }

    /**
     * @param BlockInterface $block
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($block)
    {
        if (!$block instanceof BlockInterface) {
            throw new TransformerParameterTypeException();
        }
        $facade = $this->newFacade();

        $facade->component = $block->getComponent();
        $facade->label = $block->getLabel();
        $facade->style = $block->getStyle();
        $facade->id = $block->getId();
        $facade->transverse = $block->isTransverse();
        $facade->updatedAt = $block->getUpdatedAt();
        $facade->category = $this->blockConfigurationManager->getBlockCategory($block->getComponent());

        foreach ($block->getAttributes() as $key => $attribute) {
            if (is_array($attribute)) {
                $attribute = json_encode($attribute);
            }
            $facade->addAttribute($key, $attribute);
        }

        $facade->previewContent = $this->displayBlockManager->show($block);

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'block';
    }
}
