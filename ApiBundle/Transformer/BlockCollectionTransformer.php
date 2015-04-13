<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ApiBundle\Facade\BlockCollectionFacade;

/**
 * Class ContentCollectionTransformer
 */
class BlockCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed, $nodeId = null, $currentSite = null)
    {
        $facade = new BlockCollectionFacade();

        foreach ($mixed as $block) {
            $facade->addLoadBlock($this->getTransformer('block')->transform($block, false, $nodeId));
        }

        $blocks = array();
        if ($currentSite) {
            $blocks = $currentSite->getBlocks();
            if (count($blocks) == 0) {
                $blocks = $this->getParameter('open_orchestra.blocks');
            }
        }
        foreach($blocks as $block){
            $facade->addGenerateBlock($this->getTransformer('generate_block')->transform($block));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'block_collection';
    }
}
