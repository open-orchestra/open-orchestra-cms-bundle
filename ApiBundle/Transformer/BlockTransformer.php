<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Context\CMSGroupContext;
use OpenOrchestra\Backoffice\Manager\BlockConfigurationManager;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\Backoffice\DisplayBlock\DisplayBlockManager;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class BlockTransformer
 */
class BlockTransformer extends AbstractTransformer
{
    protected $displayBlockManager;
    protected $blockConfigurationManager;
    protected $translator;
    protected $nodeRepository;

    /**
     * @param string                    $facadeClass
     * @param DisplayBlockManager       $displayBlockManager
     * @param BlockConfigurationManager $blockConfigurationManager
     * @param TranslatorInterface       $translator
     * @param NodeRepositoryInterface   $nodeRepository
     */
    public function __construct(
        $facadeClass,
        DisplayBlockManager      $displayBlockManager,
        BlockConfigurationManager $blockConfigurationManager,
        TranslatorInterface $translator,
        NodeRepositoryInterface $nodeRepository
    ) {
        parent::__construct($facadeClass);
        $this->displayBlockManager = $displayBlockManager;
        $this->blockConfigurationManager = $blockConfigurationManager;
        $this->translator = $translator;
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
        $facade->name = $this->blockConfigurationManager->getBlockComponentName($block->getComponent());
        $facade->label = $block->getLabel();
        $facade->style = $block->getStyle();
        $facade->id = $block->getId();
        $facade->language = $block->getLanguage();
        $facade->transverse = $block->isTransverse();
        $facade->updatedAt = $block->getUpdatedAt();
        $categoryKey = $this->blockConfigurationManager->getBlockCategory($block->getComponent());
        $facade->category = array(
            'label' => $this->translator->trans($categoryKey),
            'key' => $categoryKey
        );

        foreach ($block->getAttributes() as $key => $attribute) {
            if (is_array($attribute)) {
                $attribute = json_encode($attribute);
            }
            $facade->addAttribute($key, $attribute);
        }

        $facade->previewContent = $this->displayBlockManager->show($block);

        if ($this->hasGroup(CMSGroupContext::BLOCKS_NUMBER_USE)) {
            $facade->numberUse = $this->nodeRepository->countBlockUsed($block->getId());
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
