<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\ApiBundle\Facade\BlockFacade;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\DisplayBundle\DisplayBlock\DisplayBlockManager;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\Backoffice\DisplayIcon\DisplayManager;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class BlockTransformer
 */
class BlockTransformer extends AbstractTransformer
{
    protected $displayBlockManager;
    protected $displayIconManager;
    protected $nodeRepository;
    protected $translator;

    /**
     * @param string                   $facadeClass
     * @param DisplayBlockManager      $displayBlockManager
     * @param DisplayManager           $displayIconManager
     * @param TranslatorInterface      $translator
     * @param NodeRepositoryInterface  $nodeRepository
     */
    public function __construct(
        $facadeClass,
        DisplayBlockManager $displayBlockManager,
        DisplayManager $displayIconManager,
        NodeRepositoryInterface $nodeRepository,
        TranslatorInterface $translator
    )
    {
        parent::__construct($facadeClass);
        $this->displayBlockManager = $displayBlockManager;
        $this->displayIconManager = $displayIconManager;
        $this->nodeRepository = $nodeRepository;
        $this->translator = $translator;
    }

    /**
     * @param BlockInterface $block
     * @param boolean        $isInside
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform(
        $block,
        $isInside = true
    )
    {
        if (!$block instanceof BlockInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->method = $isInside ? BlockFacade::GENERATE : BlockFacade::LOAD;
        $facade->component = $block->getComponent();
        $facade->label = $block->getLabel();
        $facade->style = $block->getStyle();
        $facade->id = $block->getId();

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

        $facade->addLink('_self_form', $this->generateRoute('open_orchestra_backoffice_block_new',
            array(
                'component' => $block->getComponent(),
            )
        ));
//         if($block->getId()) {
//             $facade->addLink('_self_form', $this->generateRoute('open_orchestra_backoffice_block_form',
//                 array(
//                     'blockId' => $block->getId()
//                 )
//             ));
//         }

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
