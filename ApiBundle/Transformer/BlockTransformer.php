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
     * @param int|null       $areaId
     * @param int|null       $blockPosition
     *
     * @return FacadeInterface
     */
    public function transform($mixed, $isInside = true, $nodeId = null, $blockNumber = null, $areaId = 0, $blockPosition = 0)
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

        if (!empty ($attribute)) {
            $html = $this->displayBlockManager->show($mixed)->getContent();
        } else {
            $html = null;
        }

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
            $facade->addLink('_self_remove', $this->getRouter()->generate('php_orchestra_api_area_remove_block',
                array(
                    'nodeId' => $nodeId,
                    'areaId' => $areaId,
                    'blockPosition' => $blockPosition
                ),
                UrlGeneratorInterface::ABSOLUTE_URL
            ));
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
