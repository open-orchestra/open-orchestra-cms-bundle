<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\DisplayBundle\DisplayBlock\DisplayBlockManager;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;

/**
 * Class BlockTransformer
 */
class BlockTransformer extends AbstractTransformer
{
    protected $displayBlockManager;
    protected $nodeRepository;

    /**
     * @param string                   $facadeClass
     * @param DisplayBlockManager      $displayBlockManager
     * @param NodeRepositoryInterface  $nodeRepository
     */
    public function __construct(
        $facadeClass,
        DisplayBlockManager $displayBlockManager,
        NodeRepositoryInterface $nodeRepository
    )
    {
        parent::__construct($facadeClass);
        $this->displayBlockManager = $displayBlockManager;
        $this->nodeRepository = $nodeRepository;
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

        foreach ($block->getAttributes() as $key => $attribute) {
            if (is_array($attribute)) {
                $attribute = json_encode($attribute);
            }
            $facade->addAttribute($key, $attribute);
        }


        $facade->uiModel = $this->getTransformer('ui_model')->transform(array(
            'html' => $this->displayBlockManager->show($block)->getContent()
        ));

        $facade->isDeletable = true;
        if ($this->nodeRepository->isBlockUsed($block->getId())) {
            $facade->isDeletable = false;
        }

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
