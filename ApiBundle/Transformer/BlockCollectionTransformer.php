<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ApiBundle\Facade\BlockCollectionFacade;

/**
 * Class BlockCollectionTransformer
 */
class BlockCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection      $blockCollection
     * @param ArrayCollection|null $generateMixed
     * @param string|null          $nodeId
     *
     * @return FacadeInterface
     */
    public function transform($blockCollection, $generateMixed = null, $nodeId = null)
    {
        $facade = new BlockCollectionFacade();

        foreach($generateMixed as $block) {
            $facade->addBlock($this->getTransformer('block')->transform($block, true));
        }

        foreach ($blockCollection as $key => $block) {
            $facade->addBlock($this->getTransformer('block')->transform($block, false, $nodeId, $key));
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
